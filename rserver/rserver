library("DBI")
library("RMySQL")
library("Cairo")
# declare further packages you want to rely on here, e.g.
# library("ggplot2")

sqlbatch<-dbConnect(MySQL(), user="Rbatch", password="Rbatch", dbname="R", host="127.0.0.1")

waitseconds<-5

while (TRUE) {
 lockjob <- dbGetQuery(sqlbatch, "LOCK TABLE jobs WRITE, environment READ")
 nextjob <- dbGetQuery(sqlbatch, "SELECT jobs.ID, jobs.Status, jobs.Commands, environment.Commands Envir
				  FROM jobs JOIN environment ON (jobs.Environment = environment.ID)
				  WHERE Status='Queued' ORDER BY jobs.ID ASC LIMIT 1")
 if ((nrow(nextjob) == 1) && (nextjob$ID > 0)) {
  dbGetQuery(sqlbatch, paste("UPDATE jobs SET Status='Running', Started=NOW() WHERE ID = ", nextjob$ID))
  lockjob <- dbGetQuery(sqlbatch, paste("SELECT ID, Status FROM jobs WHERE ID = ", nextjob$ID))
  if ((nrow(lockjob) == 1) && (lockjob$ID == nextjob$ID) && (lockjob$Status == "Running")) { 
   lockjob <- dbGetQuery(sqlbatch, "UNLOCK TABLES")
   write(paste("Running Job", nextjob$ID), "")
   waitseconds <- 0

   #######################
   #
   # Retrieve Job
   #
   #######################

   parsesuccess <- FALSE
   result <- tryCatch({
    commands <- strsplit(nextjob$Commands, "\r\n")[[1]] 
    parsecmd <- parse(text=commands)
    envircommands <- strsplit(nextjob$Envir, "\r\n")[[1]] 
    parseenvir <- parse(text=envircommands)
    parsesuccess <- TRUE
   }, warning = function(w) return(w), error = function(e) return(e))
   if (!parsesuccess) {
    write(paste("Job", nextjob$ID, "failed with parsing error"), "")
    dbGetQuery(sqlbatch, paste("UPDATE jobs SET Status='Parsing-Fail', Completed=NOW(), Result=\"", dbEscapeStrings(sqlbatch, paste(result)), "\" WHERE ID = ", nextjob$ID, sep=""))
   } else {
  
    #######################
    #
    # Prepare Environment
    #
    #######################

    local({
#    if you want to use a default SQL database in your scripts, connect here
#    sql<-dbConnect(MySQL(), user="scriptuser", password="scriptuserpass", dbname="research", host="127.0.0.1")
     job.id <- nextjob$ID
     job.filename <- function(filename) {
      dbGetQuery(sqlbatch, paste("INSERT INTO results (JobID, FilenameInternal) VALUES (", job.id, ", \"", dbEscapeStrings(sqlbatch, paste(filename, collapse="")), "\")", sep=""))
      retrieveID <- dbGetQuery(sqlbatch, "SELECT LAST_INSERT_ID() AS ID")
      if (nrow(retrieveID) != 1) stop("Could not register filename")
      sprintf("../frontend/images/%010d", retrieveID$ID)
     }

     execsuccess <- FALSE
     result <- tryCatch({ eval(parseenvir); execsuccess <- TRUE },
			warning = function(w) return(w), error = function(e) return(e))
     if (!execsuccess) {
      write(paste("Job", nextjob$ID, "failed environment setup"), "")
      dbGetQuery(sqlbatch, paste("UPDATE jobs SET Status='Failure', Completed=NOW(), Result=\"", dbEscapeStrings(sqlbatch, paste("Environment execution failed.\r\n", result)), "\" WHERE ID = ", nextjob$ID, sep=""))
     } else {
   
      #######################
      #
      # Run Commands
      #
      #######################
  
      execsuccess <- FALSE
      result <- tryCatch({ commandoutput <- capture.output(eval(parsecmd)); execsuccess <- TRUE },
			 warning = function(w) return(w), error = function(e) return(e))

      if (execsuccess) {
       write(paste("Job", nextjob$ID, "completed successfully"), "")
       dbGetQuery(sqlbatch, paste("UPDATE jobs SET Status='Success', Completed=NOW(), Result=\"", dbEscapeStrings(sqlbatch, paste(commandoutput, collapse="\r\n")), "\" WHERE ID = ", nextjob$ID, sep=""))
      } else {
       write(paste("Job", nextjob$ID, "failed"), "")
       dbGetQuery(sqlbatch, paste("UPDATE jobs SET Status='Failure', Completed=NOW(), Result=\"", dbEscapeStrings(sqlbatch, paste(result)), "\" WHERE ID = ", nextjob$ID, sep=""))
      }
   
     }
  
     #######################
     #
     # Cleanup
     #
     #######################
  
     lockjob <- dbDisconnect(sql)  
    })


   }

  } else {
   write(paste("Error locking job", nextjob$ID), "")
   stop("Error locking job")
  }
 
  rm(list=setdiff(ls(),c("sqlbatch", "waitseconds")))
  lockjob <- gc()

 } else {
  # quadratic back-off. Reduce the maximum of 120 seconds to pick up new jobs more quickly
  waitseconds <- min(120, 0.1 + waitseconds)
 }
 lockjob <- dbGetQuery(sqlbatch, "UNLOCK TABLES")

 if (waitseconds > 0) {
  write(sprintf("waiting %3.1f seconds", waitseconds), "");
  Sys.sleep(waitseconds)
 }
}
lockjob <- dbDisconnect(sqlbatch)

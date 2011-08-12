#!/bin/bash

ppid=$$
maxmem=0

$@ &
pid=`pgrep -P ${ppid} -n -f $1` # $! may work here but not later
while [[ ${pid} -ne "" ]]; do
    #mem=`ps v | grep "^[ ]*${pid}" | awk '{print $8}'`
        #the previous does not work with MPI
        mem=`cat /proc/${pid}/status | grep VmRSS | awk '{print $2}'`
    if [[ ${mem} -gt ${maxmem} ]]; then
    	maxmem=${mem}
    fi
    sleep 1
    savedpid=${pid}
    pid=`pgrep -P ${ppid} -n -f $1`
done
wait ${savedpid} # don't wait, job is finished
exitstatus=$?   # catch the exit status of wait, the same of $@
echo -e "Memory usage for $@ is: ${maxmem} KB. Exit status: ${exitstatus}\n"

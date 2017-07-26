#!/bin/bash

branch_list="master wip-test wip-lisem"
scrut_file="Quality.md"

sep () {
    echo -n " | " 
}

table_init () {
   
    ##### Table header #####
    sep    
    echo -n "Name"
    sep
    for branch in ${branch_list}
    do
        echo -n ${branch}
        sep
    done
    echo

    ##### Table line #####
    sep
    echo -n "--"
    sep
    for branch in ${branch_list}
    do
        echo -n "--"
        sep
    done
    echo
    
}

repo_check ()
{
    curl -f "https://github.com/${account}/${repo}/tree/${branch}" > /dev/null 2>&1
}

repo_link () {
    branch=master
    repo_check
    if [ $? -eq 0 ]
       then
           echo -n "[${repo}](https://github.com/${account}/${repo})"   
    fi
    sep
}


scrut_link () {
    sep 
    repo_link 
    for branch in ${branch_list}
    do
        repo_check
        if [ $? -eq 0 ]
        then
            echo -n "[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/${account}/${repo}/badges/quality-score.png?b=${branch})](https://scrutinizer-ci.com/g/${account}/${repo}/?branch=${branch})"
        fi
        sep 
    done
    echo
}

for search_dir in $@
do
    ########## Scrutinizer ###########
    echo "### Scrutinizer #" > ${scrut_file}
    echo  >> ${scrut_file}
    table_init >> ${scrut_file}
    
    for ff  in $(find ${search_dir} -maxdepth 2 -name 'composer.json')
    do
        fd=$(dirname ${ff})
        repo=$(basename ${fd}) 
        account=$(basename $(dirname ${fd})) 

        ########## Scrutinizer ###########
        scrut_link >> ${scrut_file}

        
    done
done


########## Scrutinizer ###########

############ Travis ##############

########## Coveralls ###########


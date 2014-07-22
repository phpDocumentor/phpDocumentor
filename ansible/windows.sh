#!/usr/bin/env bash
sudo apt-get update
sudo apt-get install -y python-software-properties
sudo add-apt-repository -y ppa:rquillo/ansible
sudo apt-get update
sudo apt-get install -y ansible
sudo ansible-playbook /vagrant/ansible/playbook.yml --connection=local
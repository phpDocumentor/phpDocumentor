#!/usr/bin/env bash
sudo apt-get update
sudo apt-get install -y python-software-properties
sudo add-apt-repository -y ppa:rquillo/ansible
sudo apt-get update
sudo apt-get install -y ansible
cp /vagrant/ansible/inventory /etc/ansible/hosts -f
chmod 666 /etc/ansible/hosts
sudo ansible-playbook /vagrant/ansible/playbook.yml --connection=local
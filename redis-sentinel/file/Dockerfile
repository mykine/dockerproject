FROM centos:latest
RUN groupadd -r redis && useradd  -r -g redis redis
RUN yum -y update &&  yum -y install epel-release && yum -y install redis && yum -y install net-tools
EXPOSE 6379







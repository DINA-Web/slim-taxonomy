FROM debian:jessie

RUN apt-get update && \
apt-get -y upgrade && \
apt-get -y install nano curl git

RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -
RUN apt-get install -y nodejs build-essential

RUN npm install -g dredd 

WORKDIR "/home"
CMD ["bash"]
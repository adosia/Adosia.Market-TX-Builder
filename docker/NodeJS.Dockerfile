FROM node:16
MAINTAINER Latheesan Kanesamoorthy <latheesan87@gmail.com>

# Create app directory
WORKDIR /usr/src/app

# Install app dependencies
COPY /nodejs/package*.json ./
RUN npm install

# Bundle app source
COPY /nodejs .

# Run the application
EXPOSE 80
CMD [ "node", "server.js" ]

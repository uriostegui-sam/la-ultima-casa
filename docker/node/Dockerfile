FROM node:18

WORKDIR /var/www/frontend

# Dependencies
RUN npm install -g @vue/cli

# Copy files to install dependencies
COPY ./frontend/package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the files
COPY ./frontend .

# 4Build the application
RUN npm run build

# Persistent volume
VOLUME /var/www/frontend/dist

EXPOSE 5173
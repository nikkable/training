FROM node:20

WORKDIR /app

COPY package*.json ./
RUN npm install --prefer-offline --no-audit

COPY . .

#RUN npm run build

EXPOSE 3000

CMD ["npm", "run", "dev"]
#CMD ["tail", "-f", "/dev/null"]
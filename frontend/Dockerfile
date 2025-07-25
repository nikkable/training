FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN rm -rf node_modules && npm install --prefer-offline --no-audit

COPY . .

#RUN npm run build

EXPOSE 3000

CMD ["npm", "run", "dev"]
FROM node:21-alpine
WORKDIR /app
COPY ./ /app
RUN npm install && npm run build
RUN npm install -g serve
EXPOSE 3000
CMD ["serve", "-s", "dist"]
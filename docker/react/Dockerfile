FROM node:22-bullseye

WORKDIR /var/www/html/src/frontend

# パッケージインストール
# 開発時は volume マウントで package.json 等を使うため、COPYは最小限でもOK
COPY src/frontend/package.json src/frontend/package-lock.json ./
RUN npm install

EXPOSE 5173
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

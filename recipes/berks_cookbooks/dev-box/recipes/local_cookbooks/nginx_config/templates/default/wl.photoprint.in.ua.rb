# wl.photoprint.in.ua config

upstream wl {
    server localhost:3000;
    keepalive 512;
}

server {
    server_name  www.wl.photoprint.in.ua;
    charset utf-8;
    rewrite ^(.*) http://wl.photoprint.in.ua$1 permanent;
}

server {
    server_name wl.photoprint.in.ua;
    charset utf-8;

    gzip  on;
    gzip_vary on;
    gzip_comp_level  4;
    gzip_disable "MSIE [1-6]\.(?!.*SV1)";
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript application/x-javascript text/javascript;

    location / {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header X-NginX-Proxy true;
        proxy_set_header   Connection "";
        proxy_http_version 1.1;
        proxy_pass http://wl;
        proxy_redirect off;
    }

    location ~* ^.+\.(js|css|png|jpg|jpeg|gif|ico)$ {
        root /var/www/wl.photoprint.in.ua/public;
        expires 90d;
        add_header Pragma "public";
        add_header Cache-Control "public, must-revalidate, proxy-revalidate";
        add_header X-Powered-By "W3 Total Cache/0.9.2.4";
        log_not_found off;
    }
}
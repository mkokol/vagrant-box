# photoprint.in.ua config
server {
    server_name  www.photoprint.in.ua;
    charset utf-8;
    rewrite ^(.*) http://photoprint.in.ua$1 permanent;
}

server {
    server_name photoprint.in.ua;
    charset utf-8;
    rewrite ^/(.*)/$ /$1 permanent;

    root /var/www/photoprint.in.ua;
    index index.php;

    gzip  on;
    gzip_vary on;
    gzip_comp_level  4;
    gzip_disable "MSIE [1-6]\.(?!.*SV1)";
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript application/x-javascript text/javascript;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param   APPLICATION_ENV  production;
    }

    location ~ \.(js|css|png|jpg|jpeg|gif|ico)(\?v=[0-9.]+)?$ {
        try_files $uri $uri/ /index.php?$args;
        expires 90d;
        add_header Pragma "public";
        add_header Cache-Control "public, must-revalidate, proxy-revalidate";
        add_header X-Powered-By "W3 Total Cache/0.9.2.4";
        log_not_found off;
    }

    access_log /var/log/nginx/photoprint.in.ua.access.log;
    error_log /var/log/nginx/photoprint.in.ua.error.log;
}

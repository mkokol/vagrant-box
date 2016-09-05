from fabric.api import local, env, cd
from fabric.operations import run, settings, put

# connect to EC2
env.host_string = 'ec2-54-194-205-17.eu-west-1.compute.amazonaws.com'
env.user = 'ubuntu'
env.key_filename = 'mk-ireland.pem'

def deploy():
    local("export COPYFILE_DISABLE=true")
    local("sudo rm -rf public/products")

    local("php composer.phar self-update")
    local("grunt")
    local("tar czf update-photoprint.tar.gz application public vendor build index.php build.xml composer.json composer.lock composer.phar")

    run("rm -rf /var/www/photoprint.in.ua.new");
    run("rm -rf /var/www/photoprint.in.ua.old");
    run("mkdir /var/www/photoprint.in.ua.new");
    put("./update-photoprint.tar.gz", "/var/www/photoprint.in.ua.new/")

    with cd("/var/www/photoprint.in.ua.new/"):
        run("tar -xzf update-photoprint.tar.gz")
        run("rm update-photoprint.tar.gz")
        run("ln -s /mnt/photoprint_images ./images")
        run("ln -s /mnt/photoprint_products ./public/products")
        run("chmod 777 ./public/captcha/images")

        run("grep -rli \\n application/languages/* | xargs -n 1 sed -ri ':a;N;$!ba;s/\\r\\n/ /g'")
        run("grep -rli \\n application/languages/* | xargs -n 1 sed -ri ':a;N;$!ba;s/\\n/ /g'")
        run("grep -rli \\n application/languages/* | xargs -n 1 sed -ri 's/[ ][ ]*/ /g'")
        run("grep -rli \\n application/languages/* | xargs -n 1 sed -ri 's/> </></g'")

        run("grep -rli \\n application/views/scripts/* | xargs -n 1 sed -ri ':a;N;$!ba;s/\\r\\n/ /g'")
        run("grep -rli \\n application/views/scripts/* | xargs -n 1 sed -ri ':a;N;$!ba;s/\\n/ /g'")
        run("grep -rli \\n application/views/scripts/* | xargs -n 1 sed -ri 's/[ ][ ]*/ /g'")
        run("grep -rli \\n application/views/scripts/* | xargs -n 1 sed -ri 's/> </></g'")

        run("php vendor/phing/phing/bin/phing.php -Denv=prod")

    run("mv /var/www/photoprint.in.ua /var/www/photoprint.in.ua.old");
    run("mv /var/www/photoprint.in.ua.new /var/www/photoprint.in.ua");

    with cd("/var/www/photoprint.in.ua/"):
        run("php -r 'opcache_reset();'")
        run("php composer.phar install --no-dev --no-scripts --optimize-autoloader")

    run("php  -r 'opcache_reset();'")
    run("sudo service php5-fpm restart")

    local("rm update-photoprint.tar.gz")
    local("mkdir public/products")
    local("sudo chmod -Rf 777 public/products")

pipeline {
    agent any
    parameters {
        string(name: 'BRANCH', defaultValue: 'main', description: 'Branch to build from')
        choice(name: 'APP_ENV', choices: ['Production', 'Development', 'Staging'], description: 'Select the deployment environment')
        booleanParam(name: 'USE_HTTPS', defaultValue: false, description: 'Run on HTTPS (Requires Certificate)')
        // string(name: 'CERTIFICATE_PATH', defaultValue: '', description: 'Pass Only Path to SSL Certificate (Required if HTTPS is enabled)')
        string(name: 'CERTIFICATE_NAME', defaultValue: '', description: 'Pass Path With SSL Certificate Name (Required if HTTPS is enabled)')
        string(name: 'CERTIFICATE_KEY_NAME', defaultValue: '', description: 'Pass Path With SSL Certificate Key Name (Required if HTTPS is enabled)')
    }
    environment {
        PROJECT_NAME = 'Online_Food'
        APP_URL= 'http://146.190.111.215:80/'
        DORMAIN_NAME= 'food.bunlong.site'

        DB_CONNECTION = 'pgsql'
        DB_HOST= '[IP_ADDRESS]'
        DB_PORT= '5432'
        DB_DATABASE= 'cuisine'
        DB_USERNAME= 'admin'
        DB_PASSWORD= 'admin@123'

        LARAVEL_ENV = '.env'
        DOCKER_COMPOSE_FILE = 'docker-compose.yaml'
        DOCKER_ENTRY_FILE = 'docker-entrypoint.sh'
        NGINX_FILE = './nginx/nginx.conf'

        TELEGRAM_BOT_TOKEN = '8119780035:AAHYYPcMj_5xdWVA5iD14Nh-4VzWfgEhDFw'
        TELEGRAM_CHAT_ID = '-4674725761'
    }
    stages{      
        stage('Modify Docker-Compose for Database Credential') {
            steps {
                script {
                    sh """
                    echo '***Before config ${LARAVEL_ENV}...'
                    cat ${LARAVEL_ENV}
                    echo '************************************** \n'

                    echo '***Before config ${DOCKER_COMPOSE_FILE}...'
                    cat ${DOCKER_COMPOSE_FILE}
                    echo '************************************** \n'

                    sed -i 's|POSTGRES_USER: admin|POSTGRES_USER: ${DB_USERNAME}|g' ${DOCKER_COMPOSE_FILE}
                    sed -i 's|POSTGRES_PASSWORD: admin123|POSTGRES_PASSWORD: ${DB_PASSWORD}|g' ${DOCKER_COMPOSE_FILE}
                    sed -i 's|POSTGRES_DB: cuisine|POSTGRES_DB: ${DB_DATABASE}|g' ${DOCKER_COMPOSE_FILE}

                    sed -i 's|DB_CONNECTION=pgsql|DB_CONNECTION=${DB_CONNECTION}|g' ${LARAVEL_ENV}
                    sed -i 's|DB_HOST=postgres|DB_HOST=${DB_HOST}|g' ${LARAVEL_ENV}
                    sed -i 's|DB_PORT=5432|DB_PORT=${DB_PORT}|g' ${LARAVEL_ENV}
                    sed -i 's|DB_DATABASE=cuisine|DB_DATABASE=${DB_DATABASE}|g' ${LARAVEL_ENV}
                    sed -i 's|DB_USERNAME=admin|DB_USERNAME=${DB_USERNAME}|g' ${LARAVEL_ENV}
                    sed -i 's|DB_PASSWORD=admin123|DB_PASSWORD=${DB_PASSWORD}|g' ${LARAVEL_ENV}

                    echo '************************************** \n'
                    echo '***After config ${LARAVEL_ENV}...'
                    cat ${LARAVEL_ENV}
                    echo '************************************** \n'

                    echo '***After config ${DOCKER_COMPOSE_FILE}...'
                    cat ${DOCKER_COMPOSE_FILE}
                    echo '************************************** \n'
                    """
                }
            }
        }
        stage("Config Nginx for Either HTTP / HTTPS") {
            steps {
                script {
                    sh """
                    echo '***Before Configuring ${NGINX_FILE}...'
                    cat ${NGINX_FILE}
                    echo '************************************** \n'
                    
                    if [ "${params.USE_HTTPS}" = "true" ]; then
                        sed -i 's|\\n
                        |\\nserver {
                        |\\n    listen 443 ssl;
                        |\\n    server_name ${DORMAIN_NAME};
                        |\\n    ssl_certificate ${CERTIFICATE_NAME};
                        |\\n    ssl_certificate_key ${CERTIFICATE_KEY};
                        |\\n    ssl_protocols TLSv1.2 TLSv1.3;
                        |\\n    ssl_ciphers HIGH:!aNULL:!MD5;
                        |\\n    root /var/www/html/public;
                        |\\n    index index.php index.html;
                        |\\nlocation / {
                        |\\n    try_files $uri $uri/ /index.php?$query_string;
                        |\\n}
                        |\\nlocation ~ \.php$ {
                        |\\n    include fastcgi_params;
                        |\\n    fastcgi_pass app:9000;
                        |\\n    fastcgi_index index.php;
                        |\\n    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                        |\\n}
                        |g' ${NGINX_FILE}

                        echo '************************************** \n'
                        echo '***After Configuring ${NGINX_FILE} for HTTPS...'
                        cat ${NGINX_FILE}
                        echo '************************************** \n'
                    else
                        echo 'Configuring Nginx for HTTP...'
                        sed -i 's|server_name localhost;|server_name ${DORMAIN_NAME}|g' ${DOCKER_COMPOSE_FILE}
                    fi

                    echo '***After Configuring ${NGINX_FILE}...'
                    cat ${NGINX_FILE}
                    echo '************************************** \n'
                    """
                }
            }
        }

        stage("Build and Start Containers") {
            steps {
                script {
                    sh 'docker-compose up -d --build'
                }
            }
        }
    }
    post{
        always{
           script {
                sh """
                    curl -s -X POST https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage \
                        -d chat_id=${TELEGRAM_CHAT_ID} \
                        -d parse_mode="HTML" \
                        -d disable_web_page_preview=true \
                        -d text="
                        🔔 <b>*Jenkins Build Notification*</b> 🔔
                        %0A📚<b>Stage</b>: Deploy ${PROJECT_NAME} \
                        %0A🟢<b>Status:</b> ${currentBuild.result} \
                        %0A🔢<b>Version:</b> ${params.APP_ENV}-${BUILD_NUMBER} \
                        %0A📌<b>Environment:</b> ${params.APP_ENV} \
                        %0A🔗<b>Application URL:</b> ${APP_URL} \
                        %0A👤<b>User Build:</b> ${BUILD_USER}"
                """
           }
        }
    }
}
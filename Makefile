.PHONY: help install code codecbf codecs test coveragepdf server

.DEFAULT_GOAL = help

today=$(shell date +%Y-%m-%d)
todayAll=$(shell date +%Y%m%d%H%M%S)

colorCom=\033[0;34m
colorObj=\033[0;36m
colorOk=\033[0;32m
colorErr=\033[0;31m
colorWarn=\033[0;33m
colorOff=\033[m

userConsole=dev
userApache=www-data
userComposer=rcnchris
mail=rcn.chris@gmail.com
root=$(shell pwd)

templatePhpDoc=responsive

serverName=0.0.0.0
serverPort?=8000
serverFolder=public

help: ## Aide de ce fichier
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-15s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

vendor: composer.json ## Génération du dossier vendor
	composer install -o --no-suggest

composer.lock: composer.json
	composer update -o --no-suggest

install: vendor composer.lock ## Installation et/ou mise à jour des librairies

codecbf: ## Correction de la syntaxe
	@echo -e '$(colorCom)Corrections syntaxiques$(colorOff)'
	@./vendor/bin/phpcbf

codecs: ## Tests syntaxiques
	@echo -e '$(colorCom)Tests syntaxiques$(colorOff)'
	@./vendor/bin/phpcs

code: codecbf codecs ## Vérification complète du code source

doc: code ## Générer la documentation des sources
	@echo -e '$(colorCom)Générer la documentation des sources$(colorOff)'
	@/home/dev/www/devtools/phpdoc/./vendor/bin/phpdoc -d $(root)/src -t $(root)/public/doc --template="responsive"

test: ## Tests unitaires
	@echo -e '$(colorObj)Tests unitaires$(colorOff)'
	@./vendor/bin/phpunit --stop-on-failure --coverage-html public/coverage

coveragepdf: ## Générer un PDF à partir du Coverage
	@wkhtmltopdf --orientation Landscape public/coverage/index.html public/coverage/Coverage_$(shell date +%Y%m%d%H%M%S).pdf
	
server: ## Lance un serveur de développement
	@echo -e '$(colorObj)Lance un serveur sur le $(serverName):$(serverPort)$(colorOff)'
	@php -S $(serverName):$(serverPort) -t $(serverFolder)/ -d display_errors=1

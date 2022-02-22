#   -------------------------------------------------------------
#   Makefile for Keruald monorepo
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
#   Project:        Keruald
#   Description:    Generate repository needed files to start dev
#   License:        Trivial work, not eligible to copyright
#   -------------------------------------------------------------

RM=rm -f
RMDIR=rm -rf

GENERATED_FROM_TEMPLATES=phpcs.xml phpunit.xml

#   -------------------------------------------------------------
#   Main targets
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

all: build-dev build-repo

clean: clean-dev clean-repo

regenerate: clean-repo build-repo clean-dev build-dev

#   -------------------------------------------------------------
#   Build targets for libraries development
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

build-dev: vendor

clean-dev:
	${RMDIR} vendor

vendor:
	composer update

#   -------------------------------------------------------------
#   Build targets for monorepo maintenance
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

build-repo: $(GENERATED_FROM_TEMPLATES) composer.json

clean-repo:
	${RM} $(GENERATED_FROM_TEMPLATES) composer.json

$(GENERATED_FROM_TEMPLATES):
	_utils/templates/resolve.py _templates/$@.in > $@

composer.json:
	_utils/templates/generate-compose-json.php > composer.json

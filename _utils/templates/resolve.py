#!/usr/bin/env python3

#   -------------------------------------------------------------
#   Generate a template from relevant metadata
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
#   Project:        Keruald
#   Description:    Generate a template from metadata
#   License:        BSD-2-Clause
#   Dependencies:   Jinja2
#   -------------------------------------------------------------


import sys
import yaml
from jinja2 import Environment, FileSystemLoader


METADATA_FILE = "metadata.yml"


#   -------------------------------------------------------------
#   Template
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


def prepare_template_engine():
	return Environment(
		loader=FileSystemLoader('.')
	)


def get_metadata(metadata_path):
    return yaml.safe_load(open(metadata_path))


def generate_template(template_path, metadata_path):
	env = prepare_template_engine()
	template = env.get_template(template_path)

	return template.render(get_metadata(metadata_path))


#   -------------------------------------------------------------
#   Application entry point
#   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


def run(template_path):
	content = generate_template(template_path, METADATA_FILE)
	print(content)


if __name__ == "__main__":
    argc = len(sys.argv)

    if argc < 2:
        print(f"Usage: {sys.argv[0]} <argument>", file=sys.stderr)
        sys.exit(1)

    run(sys.argv[1])

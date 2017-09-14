# Public Money Public Code – Source of publiccode.eu

[![Build Status](https://drone.fsfe.org/api/badges/pmpc/website/status.svg)](https://drone.fsfe.org/pmpc/website)

This website is the core of the "Public Money, Public Code" campaign. It is based on [Hugo](https://gohugo.io), a modern static website generator.

The page is visible at [publiccode.eu](https://publiccode.eu).


## Table of Contents

- [Translate](#translate)
- [Contribute](#contribute)
- [Build](#build)
- [HowTos and FAQs](#howtos-and-faqs)
- [Maintainers](#maintainers)
- [License](#license)


## Contribute

### Prerequisites

In order to modify the website, you need `hugo` and `git` installed on your
computer. If Hugo isn't available in your package manager, obtain it from its
[official website](https://gohugo.io). We tested the website build with Hugo
from version 0.20.7 upwards. Please make sure that you use an as recent version
as possible to avoid errors.


### Configuring Git

First of all, you'll need an account on git.fsfe.org. In the FSFE's wiki,
you'll find [information about our Git
service](https://wiki.fsfe.org/TechDocs/Git), how to get access, and some
guides for basic procedures like configuration, commits, and pushes.

As part of the campaign team you can get full write access to this repository.
Please contact one of [pmpc](https://git.fsfe.org/pmpc)'s admins to give your
account the necessary privileges.


### Cloning the website

Although the mentioned Git guides will enable you to understand how to download
the website's repository, here's a short howto: Navigate to a directory on your
computer where you want the PMPC website to be stored. In this example, it's
`FSFE/PMPC/website` in your user's directory.

```sh
mkdir -p ~/FSFE/PMPC/                           # Create the directory if it doesn't exist yet
cd ~/FSFE/PMPC/                                 # go to the newly created PMPC directory
git clone git@git.fsfe.org:pmpc/website.git     # clone the website to the folder website
```

In the newly created folder `pmpc-website` you'll find all source files the
website consists of now. The hugo files are located under `site/`, whereas in
the root directory you'll only find files informational files and those
relevant for our build process (Drone, Ansible, Docker, Apache).


## Translate

Visit [TRANSLATE.md](https://git.fsfe.org/pmpc/website/src/master/TRANSLATE.md)
for detailed instructions how to translate publiccode.eu.


## Build

To see a preview of the website you need to have Hugo installed and be able to
execute Bash scripts in your command line.

1. Navigate to the website's root directory (in the last example
   `~/FSFE/PMPC/website/`) and open a terminal window there. Type in
   `git pull`. This will get the latest changes from the server
2. In the terminal, execute `cd site/` to navigate in the right
   directory for hugo's website build. You are now in
   `~/FSFE/PMPC/website/site/`
3. Only if you added a new languages which doesn't exist on the website yet:
   Add you 2-letter language code in line 4 of the [build.sh
   file](https://git.fsfe.org/pmpc/website/src/master/site/build/build.sh#L4).
4. In your terminal, execute `./build/build.sh server`. This command
   will build the website and enable you to browse the result on your
   computer only. Open [localhost:1313](http://localhost:1313/) in your web
   browser to see it.

If you want to make changes to the official website, please read [our 
Git guides](https://wiki.fsfe.org/TechDocs/Git). There you'll find out 
about the necessary commands `pull`, `status`, `add`, `commit`, and 
`push`.

There are three ways to upload/edit files in the Git repository, sorted
by preference and complexity:
1. For more experienced Git users we recommend the Fork & Pull Request
   workflow, which you'll also find a detailed [wiki's Git 
   Guide](https://wiki.fsfe.org/TechDocs/Git/Guide:Workflow) for.
2. Advanced and interested beginners can directly commit to the
   repository ("push to master") which saves them some steps, but they
   have to ask @max.mehl or @jonas in advance to give them the necessary
   permissions. You'll find guides in the [Wiki's Git
   section](https://wiki.fsfe.org/TechDocs/Git#Guides_on_specific_actions).
3. Beginners can work directly in the [web
   interface](https://git.fsfe.org/pmpc/website) of git.fsfe.org. As
   soon as you have given write permissions by @max.mehl or @jonas, you
   can edit opened text files and upload/create new files. This is the
   least preferred option because it may cause conflicts, but it is easy
   and may give you some first experience with the system.


## HowTos and FAQs 

### Important file paths

The website structure is very easy. The most important files and directories are:

- `site/config-static.toml`: Static texts and URLs which are the same
  for any language
- `site/languages/strings.{en,fr...}.toml`: Headlines, site title, many
  texts for the various languages.
- `site/content`: Markdown-files for sub-pages like /openletter, can be
  translated
- `site/data/{en,de...}/share`: Services and their very short
  translatable strings where people can share to. Is being used in the
  "Spread" section and the left-side sharing icons
- `site/static/`: CSS, images, and Javascript files for the design.
- `site/static/css/custom.css`: File where all custom CSS code should be
  written to.
- `site/layouts/`: HTML structure (scaffold) for the website. Useful if
  you want to add another section or modify anchor links or CSS classes.
- `site/layouts/page`: Template for a sub-page like /privacy
- `site/layouts/shortcodes`: HTML/Hugo code which can be important from
  within a Markdown file
- `site/public/`: Built files which are used to display the website.
  Generated by running `hugo`.


### Add a new supporting organisation

Adding a new supporting organisation requires two steps:

1. Add a new entry in [site/data/organisations/organisations.json](https://git.fsfe.org/max.mehl/pmpc-website/src/improve-readme/site/data/organisations/organisations.json) in valid JSON format, the file should be self-explaining: *name* is the full name of the organisation, *img* is the name of the logo file (case-sensitive!), and *url* the web address of the organisation. To make sure that the file has a valid JSON syntax you can use [jsonlint.com](https://jsonlint.com/) or another tool before committing your changes.
2. Add the organisation's logo to the [site/static/img/organisations](https://git.fsfe.org/max.mehl/pmpc-website/src/improve-readme/site/static/img/organisations) directory. Please only upload PNG files with maximum 150px width or 100px height – ideally using transparency instead of white as background so we can also use it on other backgrounds some day. Consider using `pngcrush` or a similar tool to reduce the file's size and remove metadata.


### Technical information about the online build

The FSFE uses Drone to automatically deploy the PMPC website. The
website is automatically deployed when there's a push to the master
branch of the repository, as well as once an hour (to update
signatures).


## Maintainers

[@max.mehl](https://git.fsfe.org/max.mehl)


## License

GNU GPL 3.0+ © 2017 Free Software Foundation Europe (FSFE)

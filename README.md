# PMPC Web Site

This website is the core of the "Public Money, Public Code" campaign. It is based on [Hugo](https://gohugo.io/), a modern static website generator.

The page is visible at [publiccode.eu](https://publiccode.eu). Please do not spread this URL. The website requires authentication to be visited. Username is `pmpc`, password is `publicmoneypubliccode`. Please do not spread this information to outside people not involved in the website development.

## Contributing to the website

### Prerequisites

In order to modify the website, you need `hugo` and `git` installed on your computer. If Hugo isn't available in your package manager, obtain it from its [official website](https://gohugo.io). We tested the website build with Hugo from version 0.20.7 upwards. Please make sure that you use an as recent version as possible to avoid errors.

### Configuring Git

First of all, you'll need an account on git.fsfe.org. With an FSFE account (FSFE Fellowship/supporter/volunteer) you can simply login with your normal username and password. If you don't have an FSFE account yet, please contact Erik, Jonas or Max about it. Without an account, you can download this repository but you won't be able to push modifications to this repository.

As part of the campaign team you can get full write access to this repository. Please contact one of [pmpc](https://git.fsfe.org/pmpc)'s admins to give your account the necessary privileges.

In the FSFE's wiki, you'll find [information about our Git server](https://wiki.fsfe.org/TechDocs/Git) installation, and some guides for basic procedures like configuration, commits, and pushes.

### Cloning the website

Although the mentioned Git guides will enable you to understand how to download the website's repository, here's a short howto: Navigate to a directory on your computer where you want the PMPC website to be stored. In this example, it's `FSFE/PMPC/website` in your user's directory.

```sh
mkdir -p ~/FSFE/PMPC/                           # Create the directory if it doesn't exist yet
cd ~/FSFE/PMPC/                                 # go to the newly created PMPC directory
git clone git@git.fsfe.org:pmpc/website.git     # clone the website to the folder website
```

In the newly created folder `pmpc-website` you'll find all source files the website consists of now. The hugo files are located under `site/`, whereas in the root directory you'll only find files informational files and those relevant for our build process (Drone, Ansible, Docker, Apache).

### Your first modification

For more experienced Git users we recommend the Fork & Pull Request workflow, which you'll also find in the [wiki's Git Guides section](https://wiki.fsfe.org/TechDocs/Git#Guides_on_specific_actions) (not available yet). However, beginners can also directly commit to the repository which saves them some steps.

As an example, we will modify the website a bit, review the changes and push them to the repository to make them available to other users and the live website.

1. Navigate to the website's root directory (in the last example `~/FSFE/PMPC/website/`) and open a terminal window there. Type in `git pull`. This will get the latest changes from the server
2. After you received the latest version, you can edit the website. For testing purposes, open the file `CONTRIBUTORS.md`, scroll to the very end, and add your name there. No worries, this won't have any visible effect on the website.
3. In the terminal, execute `cd site/` to navigate in the right directory for hugo's website build.
4. Now check whether the website looks fine. Execute `hugo server` on your terminal. You will see a link containing `localhost:1313`. Open it and you see a preview of what the website looks like. This will help you to understand whether your changes actually have the effect you wanted.
5. If you're happy with it, you can execute `hugo`. This will build the website in its final form to the subfolder `public`. Make sure that the output of that command doesn't contain any errors or warnings. Note that your local built won't be sent to the server because it builds the website itself.
6. Check with `git status` what files have been changed on your side. In this example, you should see the file `../CONTRIBUTORS.md` marked red. This means that the file has been changed but you didn't mark it as to be pushed to the server yet. If there're more files listed, make sure that you actually intended to modify these files!
7. Add the changed file to the commit queue with `git add ../CONTRIBUTORS.md`. In case you have changed more files, you can also type `git add .` in the repositories root level to add all files at once. With `git status` you should see all files marked green now, which means that they're ready to be committed.
8. Commit the files with `git commit -m "added myself to the contributors list"`. In the comment after `-m` you should always write something meaningful that helps people to understand what you actually did.
9. Unlike with SVN, you're not finished yet. You will have to execute `git push` to actually upload the modifications to the server.


## Important file paths

The website structure is very easy. The most important files and directories are:

- `site/config-static.toml`: Static texts and URLs which are the same for any language
- `site/languages/strings.{en,fr...}.toml`: Headlines, site title, many texts for the various languages.
- `site/content`: Markdown-files for sub-pages like /openletter, can be translated
- `site/data/{en,de...}/share`: Services and their very short translatable strings where people can share to. Is being used in the "Spread" section and the left-side sharing icons
- `site/static/`: CSS, images, and Javascript files for the design.
- `site/static/css/custom.css`: File where all custom CSS code should be written to.
- `site/layouts/`: HTML structure (scaffold) for the website. Useful if you want to add another section or modify anchor links or CSS classes.
- `site/layouts/page`: Template for a sub-page like /privacy
- `site/layouts/shortcodes`: HTML/Hugo code which can be important from within a Markdown file
- `site/public/`: Built files which are used to display the website. Generated by running `hugo`.

## Technical information about building

The FSFE uses Drone to automatically deploy the PMPC website. The website is
automatically deployed when there's a push to the master branch of the
repository, as well as once an hour (to update signatures).

This is the latest build status:

[![Build Status](https://drone.fsfe.org/api/badges/pmpc/website/status.svg)](https://drone.fsfe.org/pmpc/website)


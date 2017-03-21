# PMPC Web Site

This website is the core of the "Public Money, Public Code" campaign. It is based on [Hugo](https://gohugo.io/), a modern static website generator.

Since the page is still in an early draft version, the page hasn't been released officially yet. However, you can see the current status of the `public` folder on [pmpc.mehl.mx](http://pmpc.mehl.mx). Please do not spread this URL.

## Contributing to the website

### Prerequisites

In order to modify the website, you need `hugo` and `git` installed on your computer. If Hugo isn't available in your package manager, obtain it from its [official website](https://gohugo.io). The latest Hugo version is 0.19, older versions haven't been tested. Please make sure that you use an as recent version as possible to avoid errors.

### Configuring Git

First of all, you'll need an account on git.fsfe.org. With an FSFE account (FSFE Fellowship/supporter/volunteer) you can simply login with your normal username and password. If you don't have an FSFE account yet, please contact Erik, Jonas or Max about it. Without an account, you can download this repository but you won't be able to push modifications to this repository.

As part of the campaign team you can get full write access to this repository. Please contact one of [pmpc](https://git.fsfe.org/pmpc)'s admins to give your account the necessary privileges.

The most comfortable way for you to use Git is to use SSH keys for authentication. They won't require you to type in your username and password each time. Please read a [manual from Github](https://help.github.com/articles/connecting-to-github-with-ssh/) on the usage of SSH keys with their service. Although Github looks a bit different, it may help you understanding the logic. Please replace `github.com` with `git.fsfe.org`. However, a short step-by-step list:

1. Check whether you already have an SSH key (`ls -al ~/.ssh`)
2. If not generate a new SSH key (`ssh-keygen -t rsa -b 4096 -C "your_email@example.com"`). Omit typing in a password if you are sure that you can protect your SSH private key. If you set a password, consider using `ssh-agent` to avoid having to type in the SSH key's password each time you use it.
3. Add the public SSH key to your account at git.fsfe.org. Copy the content of `.~/ssh/id_rsa.pub` in `Your Settings > SSH Keys > Add Key`.
4. Try to log in (`ssh git@git.fsfe.org`). If you read "Hi there, You've successfully authenticated, but Gitea does not provide shell access.", it's working!

Then you have to set your git identity and a required default setting for git pushes:

```sh
git config --global user.name "John Doe"
git config --global user.email johndoe@example.com
git config --global push.default simple
```

### Cloning the website

Now navigate to a directory on your computer where you want the PMPC website to be stored. In this example, it's `FSFE/PMPC/website` in your user's directory.

```sh
mkdir -p ~/FSFE/PMPC/                           # Create the directory if it doesn't exist yet
cd ~/FSFE/PMPC/                                 # go to the newly created PMPC directory
git clone git@git.fsfe.org:pmpc/website.git     # clone the website to the folder website
```

In the newly created folder `pmpc-website` you'll find all source files the website consists of now.

### Your first modification

Now we will modify the website a bit, review the changes and push them to the repository to make them available to other users and the live website.

1. Navigate to the website's root directory (in the last example `~/FSFE/PMPC/website/`) and open a terminal window there. Type in `git pull`. This will get the latest changes from the server
2. After you received the latest version, you can edit the website. For testing purposes, open the file `CONTRIBUTORS.md`, scroll to the very end, and add your name there. No worries, this won't have any visible effect on the website.
3. Now check whether the website looks fine. Execute `hugo server` on your terminal. You will see a link containing `localhost:1313`. Open it and you see a preview of what the website looks like. This will help you to understand whether your changes actually have the effect you wanted.
4. If you're happy with it, you can execute `hugo`. This will build the website in its final form to the subfolder `public`. Make sure that the output of that command doesn't contain any errors or warnings. Note that your local built won't be sent to the server because it builds the website itself.
5. Check with `git status` what files have been changed on your side. In this example, you should see the file `CONTRIBUTORS.md` marked red. This means that the file has been changed but you didn't mark it as to be pushed to the server yet. If there're more files listed, make sure that you actually intended to modify these files!
6. Add the changed file to the commit queue with `git add CONTRIBUTORS.md`. In case you have changed more files, you can also type `git add .` to add all files at once. With `git status` you should see all files marked green now, which means that they're ready to be committed.
7. Commit the files with `git commit -m "added myself to the contributors list"`. In the comment after `-m` you should always write something meaningful that helps people to understand what you actually did.
8. Unlike with SVN, you're not finished yet. You will have to execute `git push` to actually upload the modifications to the server.


## Important file paths

The website structure is very easy. The most important files and directories are:

- `config.toml`: Headlines, site title, many texts.
- `data/resources/`: Content of the 3-column "resource" boxes which you can hover over and click to see the text.
- `data/share/`: Services where people can share to. Is being used in the "Spread" section and the left-side sharing icons
- `static/`: CSS, images, and Javascript files for the design.
- `static/img/share/`: Image files for share buttons.
- `static/css/custom.css`: File where all custom CSS code should be written to.
- `layouts/`: HTML structure (scaffold) for the website. Useful if you want to add another section or modify anchor links or CSS classes.
- `public/`: Built files which are used to display the website. Generated by running `hugo`.

## Technical background information

The repository will be downloaded and built every 5 minutes to the live website on pmpc.mehl.mx. So please wait a bit until your modifications are visible. It shouldn't look different from what `hugo server` shows you.

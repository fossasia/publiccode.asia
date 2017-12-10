# How to translate the site

Translating the publiccode.asia website isn't really hard but may be a bit different from what you worked with before. Before starting, please read the[README.md](./pmpc/website/src/master/README.md) file which will give you an overview of how this website is structured and built.

## Coordination

Many people want to contribute translations. 

1. To get started and join the team please do the following:

- Please become member of [FOSSASIA here](https://orgmanager.miguelpiedrafita.com/o/fossasia).
- Join the [chat channel](https://gitter.im/fossasia/fossasia)
- Write to the list that you'd like to start a translation to language

2. Translating

- Open an issue in the tracker and indicate the language or improvement you would like to make.
- Please check whether there are already ongoing translations in your
  language by reading the latest issues.
- Fork the repository to your own account and make the desired changes.
- Make a pull request.
  - Add the issue number "Fixes #AddNumberHere"
  - Add details about what you changed into the description


## Translatable files

There are a few locations where you find translatable files. All of them are inside the `site/` directory

### Content/

* In `content/` are the sub-pages like [/privacy](https://publiccode.asia/privacy). All files are written in the [Markdown syntax](https://en.wikipedia.org/wiki/Markdown).
* In the sub-directory called `openinitiative` are more files that need to be translated.

In all files you'll find a *header* which starts and ends with `---` (three dashes). In this header, all you have to translate is the `title:` value which
defines the title and headline of the page. The other values like `type` and`layout` stay the same over all languages.

The majority of the file is just text with very little markdown syntax. You should keep markup like `**`, `>`, `[fs]`, or `{{< fsdefinition >}}`. For
hyperlinks like `[TEXT](http://link)`, please only translate the content inside the quare brackets (TEXT), the link has to stay the same obviously.

### data/share/

* In `data/share/en/`, `data/share/it/` and so on there are tiny *.yaml* files for each share service we're offering (e.g. GNU Social or Diaspora). 

* There are only a few strings to translate. `titleBefore` is the text in front of the service's name, `titleAfter` the one behind. You can fill both fields to translate it. In English, this may be *Share on XYZ*, in German it is *Auf XYZ teilen*. There's also `customText` sometimes where you can find instructions how to translate it.

### languages/

Here you find one larger file for each language – e.g. `strings.en.toml` for English, `strings.de.toml` for German.

If your language isn't present, copy the file `strings.XY.toml.sample` and rename it according to your two-letter language code. Then open it and translate all strings you find (there are only a few marked which you cannot translate).

Some strings contain the Markdown links you already know (`[TEXT](LINK)`). Again, please just translate the TEXT part, not the LINK.

At some occasions you'll find a variable like `$INDS`. Leave them as is, they will automatically replaced by numbers or similar auto-generated content.

Regarding the campaign name *Public Money, Public Code*. In the past we haven't made good experiences with translating such campaign names. All our graphics, logos, and other communication is using this brand. So if you can, just stick to the English term.

### static/js/

Here there is a file called `onScrollMenu.js` which contains the abbreviations of all the languages used in an array, on the 3rd line. Add the abbreviation of the language you've added to this array. Note that these abbreviations are the same as the language code mentioned in the toml file in `site/languages`

## Where to upload the translations?

**Before submitting** the translations you can test them locally if you have Hugo installed and are able to execute Bash scripts on your command line. Please refer to [build section in README.me](./pmpc/website/src/master/README.md#build) for instructions.

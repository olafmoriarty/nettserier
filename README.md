# Nettserier.no
[Nettserier.no](https://nettserier.no) is a Norwegian webcomics portal first created in 2006. All code was remade from scratch in 2016, and this repository contains all the code from that edition.

The project is currently not open source (although it may be at some point in the future). If you want to use part of it for something, ask @olafmoriarty! :-)

## Languages
Nettserier.no is written in PHP. Jquery is supported.

## Structure and setup
* The root folder contains only index.php, .htaccess and the Adaptive Images script (hmm, maybe that script should be moved?), plus github files and this readme.
* Most project files are in the `/_ns/` folder.
* In index.php, three things happen, and all of them are includes of other files. First, the configuration of the page happens in **config.php**. Then, the content creation process happens in **content.php**. Finally, the content is output to the user's browser in **output.php**.
  * **config.php** loads all "core" functions and classes in the project, connects to the database, and contains various setup variables, including localization and logging in. As most of the code in the projects is stored in "plugins" and not in the core folder, config.php also includes configuration files for all plugins that should be active.
  * **content.php** reads the URL to figure out what content to show. If the URL is `/`, the main page is shown. If the URL is `/n/` (or `/n/something`), a page of content will be loaded. If the URL is `/foldername/` (or `/foldername/something`), the comic named **foldername** will be loaded. Based on this information content.php also creates the correct header and footer for the page.
  * **output.php** is simply a HTML document which includes content from content.php in all the right places.

### Plugins
Most of the magic of Nettserier.no happens in plugins. Lorem ipsum ...

## Plugins

_list of plugins_

## A few notes:
* The repository is made public to provide insight for our users to things like issues, etc. For the moment, though, this repository is **not** open source. Feel free to look at the code and draw inspiration from it, but please don't "steal" it quite yet. (We'll probably make it open source later, and I promise to update this readme file when we do.)
* The project language is badly-written second-language English. For now the only person working on it is Norwegian, and in practice 90+ % of our users use the Norwegian translation of the website, but since nobody can predict the future, we might as well write code (and issues etcetera) in a language the rest of the world can understand. Who knows - perhaps one day the project will have contributors who don't read Norwegian? Probably not. But it's possible.

Project manager: @olafmoriarty
Project owner: Comicopia AS

<div alt style="text-align: center; transform: scale(.5);">
	<picture>
		<source media="(prefers-color-scheme: dark)" srcset="https://github.com/surlabs/JSXGraphPC/blob/ilias8/templates/images/GitBannerJSXGraphPC.png" />
		<img alt="JSXGraphPC" src="https://github.com/surlabs/JSXGraphPC/blob/ilias8/templates/images/GitBannerJSXGraphPC.png" />
	</picture>
</div>

# JSXGraph Page Component Plugin for ILIAS 8
This plugin allows users to embed JSXGraph in ILIAS as page components

### JSXGraph is a cross-browser JavaScript library for interactive geometry, function plotting, charting, and data visualization in the web browser.  ###

See [**JSXGraph-Homepage**](http://jsxgraph.uni-bayreuth.de)


## Installation & Update

### Installation steps
1. Create subdirectories, if necessary for Customizing/global/plugins/Services/COPage/PageComponent/ or run the following script from the ILIAS root

```bash
mkdir -p Customizing/global/plugins/Services/COPage/PageComponent
cd Customizing/global/plugins/Services/COPage/PageComponent
```

3. In Customizing/global/plugins/Services/COPage/PageComponent/ **ensure you delete any previous jsxgraphpc folder**
4. Then, execute:

```bash
git clone https://github.com/surlabs/JSXGraphPC.git JSXGraph
git checkout ilias8
```

Ensure you run composer install at platform root before you install/update the plugin
```bash
composer install --no-dev
```

Run ILIAS update script at platform root
```bash
php setup/setup.php update
```

**Ensure you don't ignore plugins at the ilias .gitignore files and don't use --no-plugins option at ILIAS setup**

# Authors
* A previous version of this plugin was developed and maintained by Per Pascal Grube at the University of Stuttgart.
* This plugin is created and maintained by Jesús Copado, Saúl Díaz and Daniel Cazalla through [SURLABS](https://surlabs.es)

# Bug Reports & Discussion
- Bug Reports: [Mantis](https://www.ilias.de/mantis) (Choose project "ILIAS plugins" and filter by category "JSXGraphPC")

# Version History
* The version 9.x.x for **ILIAS 9** developed and maintained by SURLABS can be found in the Github branch **ilias9**
* The version 8.x.x for **ILIAS 8** developed and maintained by SURLABS can be found in the Github branch **ilias8**
* The version 7.x.x for **ILIAS 7** developed and maintained by SURLABS can be found in the Github branch **ilias7**
* The previous plugin versions for ILIAS <8 is archived. It can be found in [https://github.com/TIK-NFL/jsxgraphpc](https://github.com/TIK-NFL/jsxgraphpc)

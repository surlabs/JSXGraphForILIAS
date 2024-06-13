<div alt style="text-align: center; transform: scale(.5);">
	<picture>
		<source media="(prefers-color-scheme: dark)" srcset="https://github.com/surlabs/Panopto/blob/ilias8/templates/images/GitBannerPanopto.png" />
		<img alt="Panopto" src="https://github.com/surlabs/Panopto/blob/ilias8/templates/images/GitBannerPanopto.png"" />
	</picture>
</div>

# JSXGraph Page Component Plugin for ILIAS 8
This plugin allows users to embed JSXGraph in ILIAS as page components

### JSXGraph is a cross-browser JavaScript library for interactive geometry, function plotting, charting, and data visualization in the web browser.  ###

This Plugin will add a Page Component, that allows:
* Euclidean Geometry:
* Points, lines, circle, intersections, perpendicular lines, angles
* Curve plotting: Graphs, parametric curves, polar curves, data plots, Bezier curves
* Differential equations
* Turtle graphics
* Lindenmayer systems
* Interaction via sliders
* Animations
* Polynomial interpolation, spline interpolation
* Tangents, normals
* Basic support for charts
* Vectors
* ...

See [**JSXGraph-Homepage**](http://jsxgraph.uni-bayreuth.de)

###Installation

Start at your ILIAS root directory  
```bash
mkdir -p Customizing/global/plugins/Services/COPage/PageComponent  
cd Customizing/global/plugins/Services/COPage/PageComponent
git clone --recursive https://github.com/TIK-NFL/jsxgraphpc.git JSXGraph
```  
and activate it in the ILIAS-Admin-GUI. 

### Credits ###
* Developed for ILIAS 5.1 by Per Pascal Grube, University Stuttgart, 2016

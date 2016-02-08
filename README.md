Explain GTM
==========

**Explain GTM** takes Google Tag Manager container export (via a POST), parses it and renders it in easily navigable HTML using **plain English**.

You'll find it useful uf you're not technical and you want to understand GTM assets better. 

You'll find this useful if you're an experienced practitioner and you want more power.

You'll find this useful if you're an agency and you need quick and easy read only access to a new client's GTM container without having to be granted access via admin.

----------


Components
-------------
Explain GTM uses bootstrap styles.

HTML - gtm2en.html is a basic form that lets you upload a container.

Javascript - minimal ux.js handles hiding and showing of assets and asset lists,.

php - The engine room. To parse a container, post it to gtm2en.php using a form variable named "container".  Assuming the format is valid, we extract tags, triggers, variables, user defined variables, built in variables and folders.  

json - this is the GTM container to measure Explain GTM

That's it. As few moving parts as possible. Plenty of scope for optimisation and adding more functionality so feel free to fork.

Use this script as the bookmarklet to start Explain GTM when using GTM:

javascript:(function(o,m,g){  g=o.getElementsByTagName(m)[0];var j=o.createElement(m);j.async=true;j.src='//YOURDOMAIN.com/gtm2en.js';g.parentNode.insertBefore(j,g);})(document,'script');

gtm2en.js is included in this rep along with the GTM contain export to measure the tool. Make sure you change the container ID in the markup.
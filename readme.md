Bolt Webfont Loader
======================

A Bolt Extension using [TypeKit's Webfont loader](https://github.com/typekit/webfontloader) to load fonts from a Font Service or your own Stylesheet.  

This extension allows you to use a twig tag in the head of your templates to load fonts.  

## Getting Started  

In the head of your base template or your header include place this tag just above the closing ```</head>``` tag.  
```html  
{{ webfont() }}  
```
You can then move on to setting the rest of your available options in the config file.  

### Options  

**async**: Load the TypeKit Loader script Async. This defaults to false. Using the script async results in better performance but you have to take into account a Flash of Unstyled Text [FOUT](http://help.typekit.com/customer/portal/articles/6852). To Enable this change it to true.  

```yaml
async: false  
```  

**use_cdn**: Use a CDN to deliver the TypeKit script. CDN options are below.  

```yaml
use_cdn: false  
```  

**cdn**  Three CDN's are available to use:  
Certain areas of the World may block the Google CDN version. You now have the option of using two additional CDN's which are as follows:  
 
  * Use The Google CDN for the TypeKit script. This may be a few versions behind the most current one available.  
  * Use The JSDelivr CDN version
  * Use The CDNJS CDN version  
  
```yaml  
cdn: Google  
```  


**font_service**: The Font Service you would like to use. Options are:
   - Google
   - FontDeck
   - Fonts.com
   - custom ( your own fonts )  

```yaml  
font_service: Google
```  

**font_family**: The font families you would like to use.  
example single font family:  
```yaml  
font_family: [ Droid Sans ]  
```
multiple fonts - separate each font with a comma:
```yaml
font_family: [ Montserrat, Lato ]
```  
**custom_url**: if using a custom font_service ( your own fonts ) and are loading a separate stylesheet for the fonts put the URL here. This file must be located in your current themes directory
```yaml  
custom_url: css/custom-fonts.css
```
**font_deck_id**: The ID for your fontdeck account and kit
```yaml  
font_deck_id: xxxxx  
```  

Settings for Fonts.com:  
```yaml  
projectID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
version:  12345 // (optional, flushes the CDN cache)
```

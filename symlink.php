<?php
//symlink('/home/employmecom/public_html/portal/storage/app/public',  '/home/employmecom/public_html/portal/public/storage');
symlink('/home/employmecom/portal.employme.ng/Files/storage/app/public',  '/home/employmecom/portal.employme.ng/storage');
    if ($result) 
    {
      echo ("Symlink has been created!");
    }
    else 
    {
      echo ("Symlink cannot be created!");
    }
//ln -s /path/to/target /path/to/shortcut 

?>

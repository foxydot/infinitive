<form method="get" class="searchform" id="searchform" action="<?php bloginfo('url'); ?>/">
<div><input type="text" class="field" value="search" name="s" id="s" 
onblur="if (this.value == '') {this.value = 'search';}" 
onfocus="if (this.value == 'search') {this.value = '';}" />
<input type="submit" class="submit" id="searchsubmit" value="Search" />
</div>
</form>
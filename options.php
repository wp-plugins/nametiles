<div class="wrap">
<h2>Nametiles</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('nametiles'); ?>

<p><a target="_blank" href="https://nametiles.co">Nametiles</a> lets you tag people with their
  <a href="https://passcard.info" target="_blank">Passcard</a> passname and embed
  beautiful profiles in your blog. Passcard is a blockchain-based (the technology behind Bitcoin), decentralized
identity system.</p>

<p>Tagging people is easy! Simply write a plus followed by the Passcard passname of
  the person you want to tag in your blog post or theme.  To tag me, you'd write +larry.</p>

<p><strong>This plugin has 3 features:</strong></p>
<ol>
  <li>Tag Passcard users with <a href="https://nametiles.co" target="_blank">Nametiles</a></li>
  <li>Use Passcard avatars as Wordpress avatars</li>
  <li>Add arbitrary Passcard data to your theme.</li>
</ol>

<table class="form-table">
<tr valign="top">
<th scope="row">Passcard Endpoint (advanced):</th>
<td><input type="url" name="passcard_endpoint" value="<?php echo get_option('passcard_endpoint'); ?>" required /></td>
</tr>
<tr>
  <td colspan="2"><p>Recommended endpoint: <code>https://api.nametiles.co/v1/users/</code></p></td>
</tr>

</table>


<input type="hidden" name="action" value="update" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>

<h3>Nametiles</h3>
<p>Nametiles lets you tag people on your website by their Passcard much like you
would on Facebook or Twitter. Your visitors see beautiful hovercards when they mouseover
the tagged names. <a target="_blank" href="https://nametiles.co">Learn more and see Nametiles in action.</a></p>

<h3>Passcard Avatars</h3>
<p>Your users can keep their Wordpress avatar in sync with their Passcard
avatar. Each user can specify their Passcard passname in the "Your Profile" section of their account.</p>

<p>Changes to your Passcard avatar will take about an hour and or two to be
  reflected in your Wordpress installation. This is because changes to an Passcard
  take about 20 minutes to propagate and we also cache Passcard
   avatar URLs locally in Wordpress for an hour to improve performance of your site.</p>
<h3>Adding Passcard Data to Themes</h3>
<p>You can add Passcard data to your theme so that it is always up to date.</p>
<pre><code>&lt;?php $person = Passcard(&quot;larry&quot;); // load the Passcard
echo $person-&gt;name_formatted();  // User's name
echo $person-&gt;cover_url(); // User's cover picture URL ?&gt;</code></pre>

<h3>Endpoints (advanced)</h3>

<p>You can run your own Passcard endpoint and set it below or use the recommended endpoint.</p>




</div>

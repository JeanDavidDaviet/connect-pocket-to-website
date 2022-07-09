<style>.shortcode_creator_group ul { padding-left: 10px; }</style>
<h2><?php _e('How to display a feed on your WordPress site', 'pocket-to-website'); ?></h2>
<p><?php _e(sprintf('This plugin gives you the possibility to display a list of feed via the use of %1$sshortcodes%2$s.', '<a href="https://wordpress.com/support/shortcodes/" target="_blank">', '</a>'), 'pocket-to-website'); ?><br />
    <?php _e('', ''); ?></br />
    <?php _e('Then paste it wherever you want in your WordPress website.', 'pocket-to-website'); ?></p>
<p><input id="shortcode_display" type="text" value="[pocket-to-wordpress]" style="width: 100%;"></p>

<div class="shortcode_creator">
    <div class="shortcode_creator_group">
        <p><strong><?php _e('State', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: state)</strong></p>
        <ul>
            <li>
                <input type="radio" id="state_unread" name="state" value="unread" />
                <label for="state_unread"><?php _e('Unread', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="state_archive" name="state" value="archive" />
                <label for="state_archive"><?php _e('Archive', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="state_all" name="state" value="all" />
                <label for="state_all"><?php _e('All', 'pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('favorite', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: favorite)</strong></p>
        <ul>
            <li>
                <input type="radio" id="favorite_0" name="favorite" value="0" />
                <label for="favorite_0"><?php _e('Only return un-favorited items', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="favorite_1" name="favorite" value="1" />
                <label for="favorite_1"><?php _e('Only return favorited items', 'pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Tag', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: tag)</strong></p>
        <ul>
            <li>
                <input type="radio" id="tag_tagged" name="tag"/>
                <label for="tag_tagged"><?php _e('Only return items tagged with', 'pocket-to-website'); ?> <input type="text" name="tag"></label>
            </li>
            <li>
                <input type="radio" id="tag_1_untagged" name="tag" value="_untagged_" />
                <label for="tag_1_untagged"><?php _e('Only return untagged items', 'pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Content type', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: contentType)</strong></p>
        <ul>
            <li>
                <input type="radio" id="contentType_article" name="contentType" value="article" />
                <label for="contentType_article"><?php _e('Only return articles', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="contentType_video" name="contentType" value="video" />
                <label for="contentType_video"><?php _e('Only return videos or articles with embedded videos', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="contentType_image" name="contentType" value="image" />
                <label for="contentType_image"><?php _e('Only return images', 'pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Sort', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: sort)</strong></p>
        <ul>
            <li>
                <input type="radio" id="sort_newest" name="sort" value="newest" />
                <label for="sort_newest"><?php _e('Return items in order of newest to oldest', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_oldest" name="sort" value="oldest" />
                <label for="sort_oldest"><?php _e('Return items in order of oldest to newest', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_title" name="sort" value="title" />
                <label for="sort_title"><?php _e('Return items in order of title alphabetically', 'pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_site" name="sort" value="site" />
                <label for="sort_site"><?php _e('Return items in order of url alphabetically', 'pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Search', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: search)</strong><br /><?php _e('Only return items whose title or url contain the search string', 'pocket-to-website'); ?></p>
        <input type="text" name="search">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Domain', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: domain)</strong><br /><?php _e('Only return items from a particular domain', 'pocket-to-website'); ?></p>
        <input type="text" name="domain">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Count', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: count)</strong><br /><?php _e('Only return count number of items', 'pocket-to-website'); ?></p>
        <input type="number" name="count" step="1" min="0">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php _e('Offset', 'pocket-to-website'); ?> (<?php _e('filter', 'pocket-to-website'); ?>: offset)</strong><br /><?php _e('Used only with count; start returning from offset position of results', 'pocket-to-website'); ?></p>
        <input type="number" name="offset" step="1" min="0">
    </div>
</div>

<script>
    const shortcodeDisplay = document.getElementById('shortcode_display');
    Array.from(document.querySelectorAll('.shortcode_creator input')).forEach(input => {
        input.addEventListener('change', calculateShortcode)
    });
    function calculateShortcode(){
        let shortcode = '';

        Array.from(document.querySelectorAll('.shortcode_creator input[type="radio"]')).forEach(input => {
            if(input.checked && input.value !== 'on'){
                shortcode += ` ${input.name}="${input.value}"`;
            }
        });

        Array.from(document.querySelectorAll('.shortcode_creator input[type="text"], .shortcode_creator input[type="number"]')).forEach(input => {
            if(input.value.trim() !== ''){
                shortcode += ` ${input.name}="${input.value.trim()}"`;
            }
        });

        shortcodeDisplay.value = `[pocket-to-website${shortcode}]`;
    }
</script>
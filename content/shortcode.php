<style>.shortcode_creator_group ul { padding-left: 10px; }</style>
<h2><?php esc_html_e('How to display a feed on your WordPress site', 'connect-pocket-to-website'); ?></h2>
<p><?php printf(__('This plugin gives you the possibility to display a list of feed via the use of %1$sshortcodes%2$s.', 'connect-pocket-to-website'), '<a href="https://wordpress.com/support/shortcodes/" target="_blank">', '</a>'); ?><br />
    <?php esc_html_e('Then paste it wherever you want in your WordPress website.', 'connect-pocket-to-website'); ?></p>
<p><input id="shortcode_display" type="text" value="[connect-pocket-to-wordpress]" style="width: 100%;"></p>

<div class="shortcode_creator">
    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('State', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: state)</strong></p>
        <ul>
            <li>
                <input type="radio" id="state_unread" name="state" value="unread" />
                <label for="state_unread"><?php esc_html_e('Unread', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="state_archive" name="state" value="archive" />
                <label for="state_archive"><?php esc_html_e('Archive', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="state_all" name="state" value="all" />
                <label for="state_all"><?php esc_html_e('All', 'connect-pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('favorite', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: favorite)</strong></p>
        <ul>
            <li>
                <input type="radio" id="favorite_0" name="favorite" value="0" />
                <label for="favorite_0"><?php esc_html_e('Only return un-favorited items', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="favorite_1" name="favorite" value="1" />
                <label for="favorite_1"><?php esc_html_e('Only return favorited items', 'connect-pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Tag', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: tag)</strong></p>
        <ul>
            <li>
                <input type="radio" id="tag_tagged" name="tag"/>
                <label for="tag_tagged"><?php esc_html_e('Only return items tagged with', 'connect-pocket-to-website'); ?> <input type="text" name="tag"></label>
            </li>
            <li>
                <input type="radio" id="tag_1_untagged" name="tag" value="_untagged_" />
                <label for="tag_1_untagged"><?php esc_html_e('Only return untagged items', 'connect-pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Content type', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: contentType)</strong></p>
        <ul>
            <li>
                <input type="radio" id="contentType_article" name="contentType" value="article" />
                <label for="contentType_article"><?php esc_html_e('Only return articles', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="contentType_video" name="contentType" value="video" />
                <label for="contentType_video"><?php esc_html_e('Only return videos or articles with embedded videos', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="contentType_image" name="contentType" value="image" />
                <label for="contentType_image"><?php esc_html_e('Only return images', 'connect-pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Sort', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: sort)</strong></p>
        <ul>
            <li>
                <input type="radio" id="sort_newest" name="sort" value="newest" />
                <label for="sort_newest"><?php esc_html_e('Return items in order of newest to oldest', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_oldest" name="sort" value="oldest" />
                <label for="sort_oldest"><?php esc_html_e('Return items in order of oldest to newest', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_title" name="sort" value="title" />
                <label for="sort_title"><?php esc_html_e('Return items in order of title alphabetically', 'connect-pocket-to-website'); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_site" name="sort" value="site" />
                <label for="sort_site"><?php esc_html_e('Return items in order of url alphabetically', 'connect-pocket-to-website'); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Search', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: search)</strong><br /><?php esc_html_e('Only return items whose title or url contain the search string', 'connect-pocket-to-website'); ?></p>
        <input type="text" name="search">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Domain', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: domain)</strong><br /><?php esc_html_e('Only return items from a particular domain', 'connect-pocket-to-website'); ?></p>
        <input type="text" name="domain">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Count', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: count)</strong><br /><?php esc_html_e('Only return count number of items', 'connect-pocket-to-website'); ?></p>
        <input type="number" name="count" step="1" min="0">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Offset', 'connect-pocket-to-website'); ?> (<?php esc_html_e('filter', 'connect-pocket-to-website'); ?>: offset)</strong><br /><?php esc_html_e('Used only with count; start returning from offset position of results', 'connect-pocket-to-website'); ?></p>
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

        shortcodeDisplay.value = `[connect-pocket-to-website${shortcode}]`;
    }
</script>
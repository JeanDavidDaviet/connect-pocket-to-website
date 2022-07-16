<?php $slug = 'connect-pocket-to-website'; ?>
<h2><?php esc_html_e('How to display a feed on your WordPress site', $slug); ?></h2>
<p><?php printf(__('This plugin gives you the possibility to display a list of feed via the use of %1$sshortcodes%2$s.', $slug), '<a href="https://wordpress.com/support/shortcodes/" target="_blank">', '</a>'); ?><br />
    <?php esc_html_e('Then paste it wherever you want in your WordPress website.', $slug); ?></p>
<p><input id="shortcode_display" type="text" value="[<?php echo esc_attr($this->slug); ?>]" style="width: 100%;"></p>

<div class="shortcode_creator">
    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('State', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: state)</strong></p>
        <ul>
            <li>
                <input type="radio" id="state_unread" name="state" value="unread" />
                <label for="state_unread"><?php esc_html_e('Unread', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="state_archive" name="state" value="archive" />
                <label for="state_archive"><?php esc_html_e('Archive', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="state_all" name="state" value="all" />
                <label for="state_all"><?php esc_html_e('All', $slug); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('favorite', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: favorite)</strong></p>
        <ul>
            <li>
                <input type="radio" id="favorite_0" name="favorite" value="0" />
                <label for="favorite_0"><?php esc_html_e('Only return un-favorited items', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="favorite_1" name="favorite" value="1" />
                <label for="favorite_1"><?php esc_html_e('Only return favorited items', $slug); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Tag', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: tag)</strong></p>
        <ul>
            <li>
                <input type="radio" id="tag_tagged" name="tag"/>
                <label for="tag_tagged"><?php esc_html_e('Only return items tagged with', $slug); ?> <input type="text" name="tag"></label>
            </li>
            <li>
                <input type="radio" id="tag_1_untagged" name="tag" value="_untagged_" />
                <label for="tag_1_untagged"><?php esc_html_e('Only return untagged items', $slug); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Content type', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: contentType)</strong></p>
        <ul>
            <li>
                <input type="radio" id="contentType_article" name="contentType" value="article" />
                <label for="contentType_article"><?php esc_html_e('Only return articles', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="contentType_video" name="contentType" value="video" />
                <label for="contentType_video"><?php esc_html_e('Only return videos or articles with embedded videos', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="contentType_image" name="contentType" value="image" />
                <label for="contentType_image"><?php esc_html_e('Only return images', $slug); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Sort', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: sort)</strong></p>
        <ul>
            <li>
                <input type="radio" id="sort_newest" name="sort" value="newest" />
                <label for="sort_newest"><?php esc_html_e('Return items in order of newest to oldest', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_oldest" name="sort" value="oldest" />
                <label for="sort_oldest"><?php esc_html_e('Return items in order of oldest to newest', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_title" name="sort" value="title" />
                <label for="sort_title"><?php esc_html_e('Return items in order of title alphabetically', $slug); ?></label>
            </li>
            <li>
                <input type="radio" id="sort_site" name="sort" value="site" />
                <label for="sort_site"><?php esc_html_e('Return items in order of url alphabetically', $slug); ?></label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Search', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: search)</strong><br /><?php esc_html_e('Only return items whose title or url contain the search string', $slug); ?></p>
        <input type="text" name="search">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Domain', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: domain)</strong><br /><?php esc_html_e('Only return items from a particular domain', $slug); ?></p>
        <input type="text" name="domain">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Count', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: count)</strong><br /><?php esc_html_e('Only return count number of items', $slug); ?></p>
        <input type="number" name="count" step="1" min="0">
    </div>

    <div class="shortcode_creator_group">
        <p><strong><?php esc_html_e('Offset', $slug); ?> (<?php esc_html_e('filter', $slug); ?>: offset)</strong><br /><?php esc_html_e('Used only with count; start returning from offset position of results', $slug); ?></p>
        <input type="number" name="offset" step="1" min="0">
    </div>
</div>
<style>.shortcode_creator_group ul { padding-left: 20px; }</style>
                    <h2><?php _e('How to display a feed on your WordPress site', 'pocket-to-wordpress'); ?></h2>
<p><?php _e(sprintf('This plugin gives you the possibility to display a list of feed via the use of %1$sshortcodes%2$s.', '<a href="https://wordpress.com/support/shortcodes/" target="_blank">', '</a>'), 'pocket-to-wordpress'); ?><br />
    <?php _e('Simply compose your shorcode with the use of the predefined filters below.', 'pocket-to-wordpress'); ?></p>
<p><input id="shortcode_display" type="text" value="[pocket-to-wordpress]" style="width: 100%;"></p>

<div class="shortcode_creator">
    <div class="shortcode_creator_group">
        <p><strong>State</strong></p>
        <ul>
            <li>
                <input type="radio" id="state_unread" name="state" value="unread" />
                <label for="state_unread">unread</label>
            </li>
            <li>
                <input type="radio" id="state_archive" name="state" value="archive" />
                <label for="state_archive">archive</label>
            </li>
            <li>
                <input type="radio" id="state_all" name="state" value="all" />
                <label for="state_all">all</label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong>Favorite</strong></p>
        <ul>
            <li>
                <input type="radio" id="favorite_0" name="favorite" value="0" />
                <label for="favorite_0">only return un-favorited items</label>
            </li>
            <li>
                <input type="radio" id="favorite_1" name="favorite" value="1" />
                <label for="favorite_1">only return favorited items</label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong>Tag</strong></p>
        <ul>
            <li>
                <input type="radio" id="tag_tagged" name="tag"/>
                <label for="tag_tagged">only return items tagged with <input type="text" name="tag"></label>
            </li>
            <li>
                <input type="radio" id="tag_1_untagged" name="tag" value="_untagged_" />
                <label for="tag_1_untagged">only return untagged items</label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong>contentType</strong></p>
        <ul>
            <li>
                <input type="radio" id="contentType_article" name="contentType" value="article" />
                <label for="contentType_article">only return articles</label>
            </li>
            <li>
                <input type="radio" id="contentType_video" name="contentType" value="video" />
                <label for="contentType_video">only return videos or articles with embedded videos</label>
            </li>
            <li>
                <input type="radio" id="contentType_image" name="contentType" value="image" />
                <label for="contentType_image">only return images</label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong>sort</strong></p>
        <ul>
            <li>
                <input type="radio" id="sort_newest" name="sort" value="newest" />
                <label for="sort_newest">return items in order of newest to oldest</label>
            </li>
            <li>
                <input type="radio" id="sort_oldest" name="sort" value="oldest" />
                <label for="sort_oldest">return items in order of oldest to newest</label>
            </li>
            <li>
                <input type="radio" id="sort_title" name="sort" value="title" />
                <label for="sort_title">return items in order of title alphabetically</label>
            </li>
            <li>
                <input type="radio" id="sort_site" name="sort" value="site" />
                <label for="sort_site">return items in order of url alphabetically</label>
            </li>
        </ul>
    </div>

    <div class="shortcode_creator_group">
        <p><strong>search</strong><br />Only return items whose title or url contain the search string</p>
        <input type="text" name="search">
    </div>

    <div class="shortcode_creator_group">
        <p><strong>domain</strong><br />Only return items from a particular domain</p>
        <input type="text" name="domain">
    </div>

    <div class="shortcode_creator_group">
        <p><strong>count</strong><br />Only return count number of items</p>
        <input type="number" name="count" step="1" min="0">
    </div>

    <div class="shortcode_creator_group">
        <p><strong>offset</strong><br />Used only with count; start returning from offset position of results</p>
        <input type="number" name="offset" step="1" min="0">
    </div>
</div>

<script>
    const shortcodeDisplay = document.getElementById('shortcode_display');
    Array.from(document.querySelectorAll('.shortcode_creator input')).forEach(input => {
        input.addEventListener('change', function(e){
            calculateShortcode();
        });
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

        shortcodeDisplay.value = `[pocket-to-wordpress${shortcode}]`;
    }
</script>
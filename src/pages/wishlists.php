<?php

/**
 * Template for wishlists.
 *
 * @author Jay Trees <github.jay@grandel.anonaddy.me>
 */

use wishthis\{Page, User, Wishlist};

$page = new page(__FILE__, 'Wishlists');
$page->header();
$page->navigation();
?>
<main>
    <div class="ui container">
        <h1 class="ui header"><?= $page->title ?></h1>
        <p>Here you can view and edit all of your wishlists.</p>

        <h2 class="ui header">View</h2>

        <div class="ui horizontal stackable segments">

            <div class="ui segment">
                <p>Please select a wishlist to view.</p>

                <div class="ui form">
                    <div class="field">
                        <label>Wishlist</label>
                        <select class="ui fluid search selection dropdown loading wishlists" name="wishlist">
                            <option value="">Loading your wishlists...</option>
                        </select>
                    </div>
                </div>

                <div class="ui divider"></div>

                <a class="ui small labeled icon button wishlist-product-add disabled">
                    <i class="add icon"></i>
                    Add a product
                </a>

                <a class="ui small labeled icon button wishlist-share disabled" target="_blank">
                    <i class="share icon"></i>
                    Share
                </a>

                <form class="ui form wishlist-delete" method="post" style="display: inline-block;">
                    <input type="hidden" name="wishlist_delete_id" />

                    <button class="ui small labeled red icon button disabled" type="submit">
                        <i class="trash icon"></i>
                        Delete
                    </button>
                </form>
            </div>

            <div class="ui segment">
                <p>General options.</p>

                <a class="ui small labeled icon button wishlist-create">
                    <i class="add icon"></i>
                    Create a wishlist
                </a>
            </div>

        </div>

        <h2 class="ui header">Products</h2>

        <div class="ui primary progress">
            <div class="bar">
                <div class="progress"></div>
            </div>
        </div>

        <div class="wishlist-cards"></div>
    </div>
</main>

<!-- Modal: Default -->
<div class="ui modal default">
    <div class="header"></div>
    <div class="content"></div>
    <div class="actions"></div>
</div>

<!-- Wishlist: Create -->
<div class="ui modal wishlist-create">
    <div class="header">
        Create a wishlist
    </div>
    <div class="content">
        <div class="description">
            <div class="ui header">Wishlist</div>
            <p>
                Choose a new name for your wishlist.
                Here's a suggestion to get you started.
            </p>

            <form class="ui form">
                <div class="field">
                    <label>Name</label>
                    <input type="text"
                           name="wishlist-name"
                           placeholder="<?= getCurrentSeason() ?>"
                           value="<?= getCurrentSeason() ?>"
                    />
                </div>
            </form>
        </div>
    </div>
    <div class="actions">
        <div class="ui approve primary button create">
            Create
        </div>
        <div class="ui deny button cancel">
            Cancel
        </div>
    </div>
</div>

<!-- Wishlist: Add a product -->
<div class="ui modal wishlist-product-add">
    <div class="header">
        Add a product
    </div>
    <div class="image content">
        <div class="ui medium image">
            <img src="/src/assets/img/no-image.svg" loading="lazy" />
        </div>
        <div class="description">
            <div class="ui header">Product</div>
            <p>Fill out the below fields to add your new product.</p>

            <form class="ui form wishlist-product-fetch" method="post">
                <input type="hidden" name="wishlist_id" />

                <div class="field">
                    <label>URL</label>
                    <input type="url" name="product_url" />
                </div>

                <input class="ui button" type="submit" value="Fetch" />
            </form>
        </div>
    </div>
    <div class="actions">
        <div class="ui primary approve button disabled">
            Add
        </div>
        <div class="ui deny button">
            Cancel
        </div>
    </div>
</div>

<!-- Wishlist: Add a product (fetch) -->
<div class="ui small modal wishlist-product-fetch">
    <div class="header">
        Incorrect URL
    </div>
    <div class="content">
        <div class="description">
            <div class="ui header">Product URLs</div>
            <p>The URL you have entered does not seem quite right. Would you like to update it with the one I found?</p>

            <div class="ui form urls">
                <div class="field">
                    <label>Current</label>
                    <input class="current" type="url" readonly />
                </div>

                <div class="field">
                    <label>Proposed</label>
                    <input class="proposed" type="url" />
                </div>
            </div>
        </div>
    </div>
    <div class="actions">
        <div class="ui primary approve button">
            Yes, update
        </div>
        <div class="ui deny button">
            No, leave it
        </div>
    </div>
</div>

<?php
$page->footer();

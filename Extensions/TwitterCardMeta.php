<?php

/**
 * Pinc
 */
class TwitterCardMeta extends DataExtension {


    /**
     * @var array
     */
    private static $db = array(
        'TwitterSite' => 'varchar(160)',
        'TwitterCreator' => 'varchar(160)',
        'TwitterTitle' => 'varchar(160)',
        'TwitterCardType' => 'varchar(160)',
        'TwitterDescription' => 'varchar(160)'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'TwitterImage' => 'Image'

    );

    /**
     * @param FieldList $fields
     * @return Object
     */
    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldsToTab('Root.Main', ToggleCompositeField::create('Twitter Graph', 'Twitter Card',
            array(
                LiteralField::create('', '<h2>&nbsp;&nbsp;&nbsp;Twitter Card <img style="position:relative;top:4px;left 4px;" src="' . Director::absoluteBaseURL() . 'twitter-card-meta/Images/twitter.png"></h2>'),
                TextField::create('TwitterSite', 'Site Handle')->setAttribute('placeholder', 'e.g @sitename')->setRightTitle('Twitter account name for the site'),
                TextField::create('TwitterCreator', 'Creator Handle')->setAttribute('placeholder', 'e.g @username')->setRightTitle('Twitter account name for the author/creator (Will default to site handle)'),
                OptionsetField::create('TwitterCardType', 'Twitter Card Type', array(
                    'summary_large_image' => 'summary with large image',
                    'summary' => 'summary',
                ), 'summary_large_image')->setRightTitle('Choose which type of twitter card you would like this page to share as.'),
                TextField::create('TwitterTitle', 'Twitter Card Title')->setAttribute('placeholder', 'e.g Description Of Page Content')->setRightTitle('Twitter title to to display on the Twitter card'),
                TextAreaField::create('TwitterDescription', '')->setRightTitle('Twitter card description goes here, automatically defaults to the content summary'),
                UploadField::create('TwitterImage', 'Twitter Card Image')->setRightTitle('Will default too the first image in the WYSIWYG editor or banner image if left blank'),
            )
        ));
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->owner->TwitterDescription == '') {
            $this->owner->TwitterDescription = $this->owner->dbObject('Content')->Summary(50);
        }
        if ($this->owner->TwitterTitle == '') {
            $this->owner->TwitterTitle = $this->owner->Title;
        }
        if ($this->owner->TwitterSite == '') {
            $this->owner->TwitterSite = SiteConfig::current_site_config()->DefaultTwitterHandle;
        }
        if ($this->owner->TwitterCreator == '') {
            $this->owner->TwitterCreator = SiteConfig::current_site_config()->DefaultTwitterHandle;
        }
        if (!$this->owner->TwitterImageID) {
            $this->owner->TwitterImageID = SiteConfig::current_site_config()->DefaultTwitterImage()->ID;
        }
    }

    public function FirstImage() {
        $pattern = ' /<img[^>]+ src[\\s = \'"]';
        $pattern .= '+([^"\'>\\s]+)/is';
        if (preg_match_all($pattern, $this->owner->Content, $match)) {
            $imageLink = preg_replace('/_resampled\/resizedimage[0-9]*-/', '', $match[1][0]);
            return (string) $imageLink;
        } else {
            return;
        }
    }


}

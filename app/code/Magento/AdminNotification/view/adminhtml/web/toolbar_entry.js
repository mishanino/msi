/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "jquery/ui",
    "domReady!"
], function ($) {
    'use strict';

    // Mark notification as read via AJAX call
    var markNotificationAsRead = function (notificationId) {
            var requestUrl = $('.notifications-wrapper .notifications-list').attr('data-mark-as-read-url');
            $.ajax({
                url: requestUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: notificationId
                },
                showLoader: false
            });
        },

        notificationCount = $('.notifications-wrapper').attr('data-notification-count'),

        // Remove notification from the list
        removeNotificationFromList = function (notificationEntry) {
            notificationEntry.remove();
            notificationCount--;
            $('.notifications-wrapper').attr('data-notification-count', notificationCount);

            if (notificationCount == 0) {
                // Change appearance of the bubble and its behavior when the last notification is removed
                $('.notifications-wrapper .notifications-list').remove();
                var notificationIcon = $('.notifications-wrapper .notifications-icon');
                notificationIcon.removeAttr('data-toggle');
                notificationIcon.off('click.dropdown');
                $('.notifications-action .notifications-counter').text('').hide();
            } else {
                $('.notifications-action .notifications-counter').text(notificationCount);
                $('.notifications-entry-last .notifications-counter').text(notificationCount);
                // Modify caption of the 'See All' link
                var actionElement = $('.notifications-wrapper .notifications-list .last .action-more');
                actionElement.text(actionElement.text().replace(/\d+/, notificationCount));
            }
        },

        // Show notification details
        showNotificationDetails = function (notificationEntry) {
            var notificationDescription = notificationEntry.find('.notifications-entry-description'),
                notificationDescriptionEnd = notificationEntry.find('.notifications-entry-description-end');

            if (notificationDescriptionEnd.length > 0) {
                notificationDescriptionEnd.addClass('_show');
            }

            if(notificationDescription.hasClass('_cutted')) {
                notificationDescription.removeClass('_cutted');
            }
        };

    // Show notification description when corresponding item is clicked
    $('.notifications-wrapper .notifications-list .notifications-entry').on('click.showNotification', function (event) {
        // hide notification dropdown
        $('.notifications-wrapper .notifications-icon').trigger('click.dropdown');

        showNotificationDetails($(this));
        event.stopPropagation();

    });

    // Remove corresponding notification from the list and mark it as read
    $('.notifications-close').on('click.removeNotification', function (event) {
        var notificationsList = $(this).closest('.notifications-list'),
            notificationEntries = notificationsList.find('.notifications-entry'),
            notificationEntry = $(this).closest('.notifications-entry'),
            notificationId = notificationEntry.attr('data-notification-id');

        markNotificationAsRead(notificationId);
        removeNotificationFromList(notificationEntry);

        // Checking for last unread notification to hide dropdown
        if (notificationEntries.length == 2 && notificationCount == 0) {
            $('.notifications-wrapper').removeClass('active')
                                       .find('.notifications-action').removeAttr('data-toggle')
                                                                     .off('click.dropdown');
        }

        event.stopPropagation();
    });

    // Hide notifications bubble
    if (notificationCount == 0) {
        $('.notifications-action .notifications-counter').hide();
    } else {
        $('.notifications-action .notifications-counter').show();
    }

});
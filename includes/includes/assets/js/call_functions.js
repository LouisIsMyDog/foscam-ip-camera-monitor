$(document).ready(function() {
    $('#datetimepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        toggleActive: true,
        startDate: $('#datetimepicker input').attr('data-date-start-date'),
        endDate: 'getEndDate',

    });
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        var emre = $(this).ekkoLightbox({
            wrapping: false,
            onNavigate: function(direction, itemIndex) {
                var requestCount = 1;
                console.log('Navigating ' + direction + '. Current item: ' + itemIndex);
                var $lightboxGallery = this._$galleryItems;
                var $this = this;
                var $page = parseInt(this._$modalFooter.find('.item-page-value').text());
                let item = this._$modalFooter.find('.item-img-value').text();
                let beg = '^[0-9]+';
                let end = '[0-9]+$';
                var currentItem = item.match(beg)[0];
                var totalItems = item.match(end)[0];
                var totalItemIndex = $lightboxGallery.length;
                console.log(currentItem);
                console.log(totalItems);
                if (direction == 'right') {
                    // var $page = ($lightboxGallery.length / 32) + 1;
                    if (itemIndex == totalItemIndex - 1) {
                        // let url = new URL(window.location.href);
                        // url.searchParams.set("page", 10);
                        // let newUrl = url.href;
                        // window.location.href = newUrl;
                        $.get("includes/api.php", { page: ($page + 1) }, function(data) {
                            console.log(data);
                            requestCount++;
                            let obj = JSON.parse(data);
                            obj.forEach(function(item) {
                                $lightboxGallery.push(item);
                            });

                            $this.navigateTo(itemIndex);
                        });
                    }
                    console.log($lightboxGallery);
                    console.log($this._galleryIndex);
                }
                if (direction == 'left') {

                    if ($page != 1 && itemIndex == 0) {
                        $.get("includes/api.php", { page: ($page - 1) }, function(data) {
                            requestCount++;
                            let x = 31;
                            let i = 32;
                            let obj = JSON.parse(data);
                            // obj.reverse();
                            obj.forEach(function(item, key) {
                                $lightboxGallery[i] = $lightboxGallery[key];
                                $lightboxGallery[key] = item;
                                // console.log(x);
                                x--;
                                i++;
                            });
                            // $lightboxGallery.length = Object.size($lightboxGallery);
                            $lightboxGallery.length = requestCount * 32;
                            console.log($lightboxGallery.length);
                            $this.navigateTo(itemIndex+32);
                        });
                        // $lightboxGallery.concat(newFirstArray);
                        // $(".item-"+itemIndex).after(data);
                        // $lightboxGallery.push(data);
                        // $this.constructor($this._$element,$this._config);
                        // console.log($this);
                        // console.log($lightboxGallery);

                    }
                    // console.log($lightboxGallery[$this._galleryIndex]);
                    console.log($lightboxGallery);
                    console.log($this._galleryIndex);
                }
            },
            onShow: function() {},
            onShown: function() {},
            alwaysShowClose: true,
        });

    });
    // $('.thumbnail a:first').ekkoLightbox();

    var fixNavbarNavPills = (function() {
        if ($('.navbar').height() > $('nav .container').height()) {
            let x = $('.navbar').height() - $('nav .container').height();
            $('nav .container').css('margin-top', (x / 2) + 'px');
            console.log(x / 2);
        }
    })();

});
// Try navigateTo() to rebuild arrows
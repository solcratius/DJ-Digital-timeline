DJTIMELINE.namespace( 'main' );

DJTIMELINE.main = (function($) { 
    var $WIN = $(window),
        $HTML = $('html'),
        $CONTAINER,
        $TIMELINE,
        $CONT_UL,
        $CONTBG_UL,
        $LOCKUP,
        $EVENT_TYPE,
        $UTILNAV,
        $BTN_Q,
        $INTRO_CONT,
        $DETAIL_CONT,
        $DETAIL_CONT_WRAP,
        $DOWNLOAD,
        $PRINT,
        $SECURE_BOX;

    var winW,
        winH,
        myDomain,
        tlURL,
        resizing,
        introPlaying,
        isDragging,
        Q_ID,
        D_ID;

    var tList = [],
        tListTotal,
        qleftP = [],
        monthW = [],
        monthX = [],
        sContainN,
        originY,
        tlX,
        ebW = [],
        dxList = [],
        monthStat = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    var init = function init() {
        $CONTAINER = $('.main-content');
        $LOCKUP = $('.footer-content');
        $EVENT_TYPE = $('.footer-content .event-type');
        $UTILNAV = $('#header .util-nav');
        $BTN_Q = $('#header .util-nav li');
        $DETAIL_CONT = $('.detail-content');
        $DETAIL_CONT_WRAP = $('.detail-content .wrapper');
        $DOWNLOAD = $('.download-content');
        $PRINT = $('#printable');

        introPlaying = true;
        ebW = [100, 180, 180, 270]; //vert, hor, vert-lg, hor-lg
        resizing = false;
        Q_ID = 0;
        myDomain = $('h1 a').attr('href');
        tlURL = $CONTAINER.find(".data").html();

        $WIN.resize(function() {
            resizing = true;
            $HTML.removeClass('animate');
            getWinD();
            checkResize();
        });

        getWinD();

        if (winH < 880) $HTML.addClass('win-short');
        else $HTML.removeClass('win-short');

        if ($HTML.hasClass('no-svg')) usePng();

        $CONTAINER.css('height', Math.floor(winH - $LOCKUP.find('.title').height()) + 'px');
        $LOCKUP.removeClass('short');

        $EVENT_TYPE.css('height', $EVENT_TYPE.height()+"px");
        $EVENT_TYPE.addClass('intro');
        $EVENT_TYPE.find('li').each(function(i) {
            var $THIS = $(this).find('a');
            tList.push($THIS.attr("class"));
        });
        tListTotal = tList.length;

        tlX = 0;
        loadTimeline("intro");
        handlers();
    };

    var checkResize = function checkResize() {
        if (resizing)
        {
            if (winH < 880) $HTML.addClass('win-short');
            else $HTML.removeClass('win-short');

            $CONTAINER.css('height', Math.floor(winH - $LOCKUP.height()) + 'px');
            originY = getContentMid();
            $CONT_UL.find('.event-box').css('top', originY+'px');

            if (!introPlaying)
            {
                sContainN = -1*($CONT_UL.width()-winW);

                if (sContainN > tlX)
                {
                    $CONT_UL.css("left", sContainN+"px");
                    $CONTBG_UL.css("left", sContainN+"px");
                }
                // console.log("sContainN:"+sContainN);
                if ($CONT_UL.data('ui-draggable')) $CONT_UL.draggable("option", "containment", [sContainN, 0, $CONTAINER.offset().left, 0]);

                BoxRangeShow(tlX);
            }

            setTimeout(function() {
                resizing = false;
                $HTML.addClass('animate');
            }, 25);
        }
    };

    var getTimelineProp = function getTimelineProp(method) {
        // tlX = 0;
        var tWidth = 0;
        var $mDiv = $CONT_UL.find('li');
        var $mBG = $CONTBG_UL.find('li');
        var mTotal = $mDiv.length - 1;
        monthX = [];
        qleftP = [];
        D_ID = 0;
        dxList = [];

        $mDiv.each(function(i) {
            var $THIS = $(this);
            var monthW = 0;
            var boxLen = $THIS.find('.event-box').length-1;
            var tallLoop = 0;
            var pppprevBoxW, pppprevBoxX, ppprevBoxW, ppprevBoxX, pprevBoxW, pprevBoxX, pprevBoxS, prevBoxW, prevBoxX, prevBoxS;

            if (i <= 0)
            {
                monthW = 30;
            }
            else
            {
                $THIS.find('.event-box').each(function(i) {
                    var $THIS = $(this);
                    var myX, myW, myS;

                    tallLoop += 1;
                    myW = ebW[0];
                    if ($THIS.hasClass('lg'))
                    {
                        if ($THIS.hasClass('hor')) myW = ebW[3];
                        else myW = ebW[2];
                        myS = "L";
                    }
                    else
                    {
                        if ($THIS.hasClass('hor')) myW = ebW[1];
                        myS = "S";
                    }

                    if (i == 1) myX = prevBoxX; //set second box pos
                    else myX = (prevBoxX + (prevBoxW * .5) + 30) - (myW * .5); //set my point to prev point + 30

                    //set first box pos
                    if (i <= 0)
                    {
                        if ($THIS.hasClass('hor') || $THIS.hasClass('lg')) myX = 20;
                        else myX = 40;
                    }

                    //Check if my box touchs pppprev box's edge and it's padding
                    if (myX < (pppprevBoxX + pppprevBoxW + 30)) myX = (pppprevBoxX + pppprevBoxW + 30);

                    //Check if my box is tall and touchs pprev box's edge and it's padding
                    if ((myX < (pprevBoxX + pprevBoxW + 30 - (myW * .5))) && tallLoop > 2) myX = pprevBoxX + pprevBoxW + 30 - (myW * .5);

                    //Check if my box touchs pprev box's line and it's padding
                    if (myX < (pprevBoxX + (pprevBoxW * .5) + 30)) myX = (pprevBoxX + (pprevBoxW * .5) + 30);

                    //Check if my point touchs prev box's point and it's padding
                    if (myX < (prevBoxX + (prevBoxW * .5) + 30) - (myW * .5)) myX = (prevBoxX + (prevBoxW * .5) + 30) - (myW * .5);

                    //Check if my lg box touchs pprev box's edge and it's padding
                    if ((myS == "L" && myW != ebW[3]) && myX < (pprevBoxX + pprevBoxW + 30)) myX = pprevBoxX + pprevBoxW + 30;
                    if ((pprevBoxS == "L" && pprevBoxW != ebW[3]) && myX < (pprevBoxX + pprevBoxW + 30)) myX = pprevBoxX + pprevBoxW + 30;

                    $THIS.css('left', myX+'px');

                    pppprevBoxW = ppprevBoxW;
                    pppprevBoxX = ppprevBoxX;
                    ppprevBoxW = pprevBoxW;
                    ppprevBoxX = pprevBoxX;
                    pprevBoxW = prevBoxW;
                    pprevBoxX = prevBoxX;
                    pprevBoxS = prevBoxS;
                    prevBoxW = myW;
                    prevBoxX = myX;
                    prevBoxS = myS;

                    if (tallLoop >= 4) tallLoop = 0;
                    if (i >= boxLen) monthW = myX + myW + 40;

                    var detailTxt = $THIS.find('.event-content span.data.detail').html();

                    if (detailTxt)
                    {
                        $THIS.find('.event-content').append('<span class="data d-id">' + D_ID + '</span>');
                        dxList.push(tWidth + myX);

                        $THIS.find('.event-content').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (!isDragging) detailContOn(Number($(this).find('span.data.d-id').html()));
                        });

                        D_ID += 1;
                    }
                });
            }

            $THIS.css('width', monthW+'px');
            $mBG.eq(i).css('width', monthW+'px');
            monthX.push(tWidth);

            tWidth = tWidth + $THIS.width();
        });
        
        $CONT_UL.css('width', tWidth+'px');
        $CONTBG_UL.css('width', tWidth+'px');
        sContainN = -1*($CONT_UL.width()-winW);
        
        for (var q = 0; q < (mTotal/3); q ++)
        {
            if (q <= 0) qleftP.push(0);
            else qleftP.push($mDiv.eq(q * 3 + 1).position().left - 30);
        }

        draggableOn();
    };

    var draggableOn = function draggableOn() {
        $CONT_UL.draggable({
            cursor: "move",//"pointer",//
            containment: [sContainN, 0, 0, 0],
            axis: "x",
            helper: function(){
                // Create an invisible div as the helper. It will move and
                // follow the cursor as usual.
                return $('<div></div>').css('opacity',0);
            },
            start: function() {
                isDragging = true;
            },
            stop: function() {
                setTimeout(function() {
                    isDragging = false;
                }, 500);
            },
            drag: function(event, ui) {
                var $THIS = $(this);
                tlX = ui.helper.position().left;

                $THIS.stop().animate({
                    left: tlX
                }, 500,'easeOutCirc');
                $CONTBG_UL.stop().animate({
                    left: tlX
                }, 500,'easeOutCirc');

                BoxRangeShow(tlX);
                // console.log("x:"+tlX);

                for (var n = 0; n < qleftP.length; n ++) if (tlX < -1*qleftP[n] + (winW * .5)) setQ(n);
            }
        });
    };

    var simulateDrag = function simulateDrag(x, t) {
        if (!t) t = 500;
        $CONT_UL.stop().animate({
            left: x
        }, t,'easeInOutCubic');
        $CONTBG_UL.stop().animate({
            left: x
        }, t,'easeInOutCubic');
    };

    var BoxRangeShow = function BoxRangeShow(n, method) {
        var rNum;

        // for (var i = 0; i < monthX.length; i ++) if (monthX[i] < ((-1 * n) + (winW-120))) rNum = i;
        for (var i = 0; i < monthX.length; i ++) if (monthX[i] < ((-1 * n) + (winW-160))) rNum = i;
        if (method == "all") rNum = 13;

        $CONT_UL.find('li').each(function(i) {
            var $THIS = $(this);

            if (i <= rNum)
            {
                $CONTBG_UL.find('li').eq(i).addClass('on');

                if (i < rNum)
                {
                    $THIS.find('.event-box').addClass('on');

                    if (method == "intro")
                    {
                        $THIS.find('.event-box').each(function(j) {
                            var $THIS = $(this);
                            $THIS.delay((250*i)+(150*j)).queue(function() {
                                $(this).addClass('init').dequeue();
                            });
                        });
                        // if (!$THIS.find('.event-box').hasClass('init')) $THIS.find('.event-box').addClass('init');
                    }
                    else
                    {
                        // if (!$THIS.find('.event-box').hasClass('init')) $THIS.find('.event-box').addClass('init');
                        $THIS.find('.event-box').addClass('init');
                    }
                }
                else
                {
                    $THIS.find('.event-box').each(function(j) {
                        $THIS = $(this);
                        var boxP = monthX[i] + $THIS.position().left + ($THIS.width() * .5);

                        if (boxP < ((-1 * n) + (winW-100)))//60)))
                        {
                            $THIS.addClass('on');

                            if (method == "intro")
                            {
                                $THIS.delay((250*i)+(150*j)).queue(function() {
                                    $(this).addClass('init').dequeue();
                                });
                                // if (!$THIS.hasClass('init')) $THIS.addClass('init');
                            }
                            else
                            {
                                // if (!$THIS.hasClass('init')) $THIS.addClass('init');
                                $THIS.addClass('init');
                            }
                        }
                        else
                        {
                            $THIS.removeClass('on');
                            if (method == "intro") $THIS.removeClass('init');
                        }
                    });
                }
            }
            else
            {
                $THIS.find('.event-box').removeClass('on');
                $CONTBG_UL.find('li').eq(i).removeClass('on');
            }
        });
    };

    var detailContOn = function detailContOn(id) {
        if ($DETAIL_CONT.is(':hidden')) $DETAIL_CONT.fadeIn(500);
        if (dxList.length <= 1) $DETAIL_CONT.find('a.arrow-btn').fadeOut(0);
        else $DETAIL_CONT.find('a.arrow-btn').fadeIn(0);

        var $THIS = $('span.data.d-id').eq(id).parent();

        var detailTxt = $THIS.find('span.data.detail').html();
        var eventName = $THIS.find('span.data.name').html();
        if (eventName == "" || eventName == null) eventName = $THIS.find('p').clone().children().remove().end().text();
        var eventImg = $THIS.find('.img img').attr('src');
        var eventDate = $THIS.find('span.data.date').html();
        var eY = Number(eventDate.substr(0, 4));
        var eM = Number(eventDate.substr(4, 2));
        var eD = Number(eventDate.substr(6, 2));

        $DETAIL_CONT_WRAP.fadeOut(250, function() {
            $DETAIL_CONT_WRAP.html("");
            $DETAIL_CONT_WRAP.append('<p class="date">' + monthStat[eM-1] + ' ' + eD + ', ' + eY + '</p>');
            if (eventImg) $DETAIL_CONT_WRAP.append('<img src="' + eventImg + '" />');
            $DETAIL_CONT_WRAP.append('<h2>' + eventName + '</h2>');
            $DETAIL_CONT_WRAP.append('<p class=txt>' + detailTxt + '</p>');

            $DETAIL_CONT_WRAP.fadeIn(250);
            D_ID = id;
            // console.log("D_ID:"+D_ID+"/"+dxList[id]);
        });
    };

    var loadTimeline = function loadTimeline(method) {
        var pageName;
        Q_ID = 0;

        if (method == "intro")
        {
            pageName = tlURL;
        }
        else
        {
            pageName = tlURL + '?';
            for (var i = 0; i < tList.length; i ++) pageName = pageName + 'cType[]=' + tList[i] + '&';
        }

        $HTML.removeClass('animate');
        // console.log("loading timeline... " + pageName);
        // removeCookie("DJ-TIMELINE-LOGIN");
        
        $.get(pageName, function (response) {
                var markup = $("<div>" + response + "</div>"),
                    fragment = markup.find(".tl-content").html();
                    // console.log("markup... " + fragment);
                $(".main-content").html(fragment);

                $TIMELINE = $('.main-content .timeline');
                $CONT_UL = $('.main-content .content ul');
                $CONTBG_UL = $('.main-content .content-bg ul');
                $INTRO_CONT = $('.main-content .intro-content');

                if (method == "intro")
                {
                    var introDelay = $INTRO_CONT.find('.data').text();
                    getTimelineProp("intro");

                    if (!$('#secureBox').length) initStart(introDelay);
                }
                else
                {
                    getTimelineProp();
                    initStart("skip");
                    introPlaying = false;
                } 
            }           
        );
    };

    // var getPageName = function getPageName() {
    //     var pathName = String(window.location),
    //         pageName = '';

    //     pageName = pathName.substring(myDomain.length);
    //     // console.log(pageName);
    //     return pageName;
    // };

    var updateType = function updateType(method, type) {
        var i = checkTList(type);

        if (method == "add" && i <= -1) tList.push(type);
        if (method == "remove" && i > -1) tList.splice(i, 1);

        if (method == "one")
        {
            tList = [];
            tList.push(type);
        }

        if (method == "all")
        {
            $EVENT_TYPE.find('li').each(function(i) {
                var $THIS = $(this).find('a');
                tList.push($THIS.attr("class"));
            });
            tListTotal = tList.length;
        }

        loadTimeline();
    };

    var removeCookie = function removeCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    };

    var IEDetect = {
        init: function () {
            return this.searchString(this.dataBrowser) || false;
        },
        searchString: function (data) {
            for (var i = 0; i < data.length; i++) {
                var dataString = data[i].string;
                if (dataString.indexOf(data[i].subString) !== -1) {
                    return true;
                }
            }
        },
        dataBrowser: [
            {string: navigator.userAgent, subString: "Edge", identity: "MS Edge"},
            {string: navigator.userAgent, subString: "MSIE", identity: "Explorer"},
            {string: navigator.userAgent, subString: "Trident", identity: "Explorer"}
        ]
    };

    var checkTList = function checkTList(t) {
        return tList.indexOf(t);
    };

    var setQ = function setQ(id) {
        $BTN_Q.eq(Q_ID).removeClass('active');
        $BTN_Q.eq(id).addClass('active');
        Q_ID = id;

        BoxRangeShow(tlX);
    };

    var getWinD = function getWinD() {
        winW = $WIN.width();
        winH = $WIN.height();
    };

    var getWinW = function getWinW() {
        return winW;
    };
    
    var getWinH = function getWinH() {
        return winH;
    };

    var getContentMid = function getContentMid() {
        return Math.floor($CONTAINER.height() * .5);
    };

    var rNumGenerator = function rNumGenerator(num) {
        return Math.floor(Math.random()*num);
    };

    var usePng = function usePng() {
        $('img').each(function() {
            var $img = $(this);
            var imgsrc = $img.attr('src');
            var ext = imgsrc.substr(imgsrc.lastIndexOf("."));

            if (ext == '.svg')
            {
                var newpath = imgsrc.substr(0, imgsrc.lastIndexOf(".")) + ".png";
                $img.attr('src',newpath);
            }
            else
            {
                return;
            }
        });
    };
    
    var initStart = function initStart(t) {
        if (t <= 0) t = "skip";

        $CONT_UL.find('li:first-child').css('width', winW+'px');
        $CONTBG_UL.find('li:first-child').css('width', winW+'px');

        if (t == "skip")
        {
            $TIMELINE.css('left', '0');
            $LOCKUP.addClass('short');
            $EVENT_TYPE.removeClass('intro');
            $EVENT_TYPE.removeAttr("style");
            $UTILNAV.removeClass('intro');
            $CONT_UL.find('li:first-child').css('width', '30px');
            $CONTBG_UL.find('li:first-child').css('width', '30px');
            originY = getContentMid();
            $CONT_UL.find('.event-box').css('top', originY+'px');
            // $BTN_Q.eq(Q_ID).addClass('active');
            $BTN_Q.removeClass('active');
            $HTML.addClass('animate');

            if (Q_ID <= 0) tlX = -1*qleftP[Q_ID];
            else tlX = -1*qleftP[Q_ID] + (winW * .5);
            if (sContainN > tlX) tlX = sContainN;

            setTimeout(function(i) {
                setQ(Q_ID);
            }, 500);

            //auto slide timeline to prev q
            // setTimeout(function(i) { 
            //     simulateDrag(tlX, 1000 * Q_ID);//1000);
            // }, 500);
        }
        else
        {
            setTimeout(function(i) {
                $TIMELINE.css('left', '-62%'); //'-60%');
                $INTRO_CONT.fadeIn(500, 'easeOutCubic');
                $HTML.addClass('animate');
            }, 50);

            var masterT;

            if (localStorage.DJ_Timeline_Visited)
            {
                masterT = 1000;
            }
            else
            {
                localStorage.DJ_Timeline_Visited = true;
                masterT = (Number(t)*1000) + 350;
            }
            // localStorage.clear(); //remove

            setTimeout(function(i) {
                $LOCKUP.addClass('short');
                $INTRO_CONT.fadeOut(250);

                setTimeout(function(i) {
                    $EVENT_TYPE.removeClass('intro');
                    $TIMELINE.css('left', '0');
                    $UTILNAV.removeClass('intro');
                    
                    BoxRangeShow(tlX, "intro");

                    $CONT_UL.find('li:first-child').animate({
                        'width': 30
                    }, 1000, 'easeInOutCubic');
                    $CONTBG_UL.find('li:first-child').animate({
                        'width': 30
                    }, 1000, 'easeInOutCubic');

                    originY = getContentMid();
                    $CONT_UL.find('.event-box').css('top', originY+'px');

                    setTimeout(function(i) {
                        $HTML.removeClass('animate');
                        $EVENT_TYPE.removeAttr("style");
                        setTimeout(function(i) {
                            $HTML.addClass('animate');
                            introPlaying = false;
                        }, 500);
                        $BTN_Q.eq(Q_ID).addClass('active');
                    }, 500);
                }, 350);
            }, masterT);
        }
    };

    function handlers() {
        $EVENT_TYPE.find('li').each(function(i) {
            var $THIS = $(this).find('a');

            $THIS.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var method;
                var cName = $THIS.attr('class');
                cName = cName.split(" ");

                if (tList.length == tListTotal)
                {
                    $EVENT_TYPE.find('li a').addClass('disable');
                    $THIS.removeClass('disable');
                    method = "one";
                }
                else
                {
                    if ($THIS.hasClass('disable'))
                    {
                        $THIS.removeClass('disable');
                        method = "add";
                    }
                    else
                    {
                        if (tList.length <= 1)
                        {
                            $EVENT_TYPE.find('li a').removeClass('disable');
                            method = "all";
                        }
                        else
                        {
                            $THIS.addClass('disable');
                            method = "remove";
                        }
                    }
                }

                updateType(method, cName[0]);
            });
        });

        var $UTIL_TOTAL = $UTILNAV.find('li').length;

        $UTILNAV.find('li').each(function(i) {
            var $THIS = $(this).find('a');

            $THIS.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (i < ($UTIL_TOTAL - 1) && !$THIS.hasClass('active'))
                {
                    tlX = -1*qleftP[i];
                    if (tlX < sContainN) tlX = sContainN;
                    simulateDrag(tlX, 1000);
                    setQ(i);
                }
                else 
                {
                    if ($DOWNLOAD.html() == "" && !IEDetect.init())
                    {
                        $CONTAINER.clone().appendTo($DOWNLOAD);
                        $UTILNAV.clone().appendTo($DOWNLOAD);
                        $DOWNLOAD.find('.main-content').removeAttr('style');
                        $DOWNLOAD.find('.content ul').removeAttr('class');
                        $DOWNLOAD.find('.content ul').removeAttr('style');
                        $DOWNLOAD.find('.content ul .event-box').addClass('init').addClass('on');
                        $DOWNLOAD.find('.content-bg ul li').addClass('on');
                        $DOWNLOAD.show();
                        var tempContH = $CONTAINER.height();
                        var totalQ = 1;//$UTILNAV.find('li').length - 1;
                        var printI = 0;

                        $CONTAINER.hide();
                        $LOCKUP.hide();
                        $('#header').hide();

                        for (var q = 0; q < totalQ; q ++)
                        {
                            q = Q_ID; // This sets to print current quarter
                            $PRINT.html("");
                            $DOWNLOAD.find('.content-bg ul li').hide();
                            $DOWNLOAD.find('.content ul li').hide();
                            $DOWNLOAD.find('.util-nav li').hide();
                            var downloadContW = 0;

                            for (var m = 0; m < 3; m ++)
                            {
                                var tempI = q * 3;
                                $DOWNLOAD.find('.content-bg ul li').eq(1 + tempI + m).show();
                                $DOWNLOAD.find('.content ul li').eq(1 + tempI + m).show();
                                downloadContW = downloadContW + $DOWNLOAD.find('.content ul li').eq(1 + tempI + m).outerWidth(true);
                            }

                            if (downloadContW < winW) downloadContW = winW; 
                            $DOWNLOAD.find('.content-bg ul').css('width', downloadContW + 'px');
                            $DOWNLOAD.find('.content ul').css('width', downloadContW + 'px');
                            $DOWNLOAD.find('.util-nav li').eq(q).show();
                            $DOWNLOAD.css('width', downloadContW + 'px');
                            $DOWNLOAD.css({
                                'transform': 'scale(3,3)',
                                '-ms-transform': 'scale(3,3)',
                                '-webkit-transform': 'scale(3,3)',
                                'transform-origin': 'top left'
                            });
                            var useWidth = $DOWNLOAD.width() * 3;
                            var useHeight = $DOWNLOAD.height() * 3;

                            html2canvas($DOWNLOAD, {
                                width: useWidth,
                                height: useHeight,
                                onrendered: function(canvas) {
                                    var imageData = canvas.toDataURL("image/png");
                                    var image = new Image();
                                    image = Canvas2Image.convertToJPEG(canvas);

                                    $PRINT.show();
                                    $PRINT.html(image);
                                    // var tempH = $PRINT.width()/1.4142;
                                    // var imgTempY = $PRINT.find('img').height() - tempH;
                                    // if (imgTempY > 0) $PRINT.find('img').css('margin-top', (-1 * (imgTempY * .5)) + 'px');
                                    $LOCKUP.find('.title').clone().appendTo($PRINT);
                                    $CONTAINER.css('height', '100px');

                                    window.print();
                                    printI ++;

                                    if (totalQ <= 1 || (printI >= q && totalQ > 1))
                                    {
                                        
                                        $CONTAINER.show();
                                        $LOCKUP.show();
                                        $('#header').show();

                                        $PRINT.html("");
                                        $PRINT.hide();
                                        
                                    }
                                }
                            });
                        }

                        $CONTAINER.css('height', tempContH+'px');
                        $DOWNLOAD.html("");
                        $DOWNLOAD.hide();
                        
                        /*
                        html2canvas($DOWNLOAD, {
                            onrendered: function(canvas) {
                                var imageData = canvas.toDataURL("image/jpeg");
                                var image = new Image();
                                image = Canvas2Image.convertToJPEG(canvas);

                                var doc = new jsPDF('landscape', 'in', 'letter');
                                // var doc = new jsPDF();
                                // doc.addImage(imageData, 'JPEG', 12, 10);
                                doc.addImage(imageData, 'JPEG', 0, 0);
                                var croppingXPosition = 1095;
                                count = (image.width) / 1095;
                                // console.log(count);
                                for (var j = 1; j < count; j ++)
                                {
                                    doc.addPage();
                                    var sourceX = croppingXPosition;
                                    var sourceY = 0;
                                    var sourceWidth = 1095;
                                    var sourceHeight = image.height;
                                    var destWidth = sourceWidth;
                                    var destHeight = sourceHeight;
                                    var destX = 0;
                                    var destY = 0;
                                    var canvas1 = document.createElement('canvas');
                                    canvas1.setAttribute('height', destHeight);
                                    canvas1.setAttribute('width', destWidth);                         
                                    var ctx = canvas1.getContext("2d");
                                    ctx.drawImage(image, sourceX, 
                                                         sourceY,
                                                         sourceWidth,
                                                         sourceHeight, 
                                                         destX, 
                                                         destY, 
                                                         destWidth, 
                                                         destHeight);
                                    var image2 = new Image();
                                    image2 = Canvas2Image.convertToJPEG(canvas1);
                                    image2Data = image2.src;
                                    // doc.addImage(image2Data, 'JPEG', 12, 10);
                                    doc.addImage(image2Data, 'JPEG', 0, 0);
                                    croppingXPosition += destWidth;
                                }               
                                var d = new Date().toISOString().slice(0, 19).replace(/-/g, ""),
                                filename = 'DJ_timeline' + d + '.pdf';
                                doc.save(filename);

                                $DOWNLOAD.html("");
                                $DOWNLOAD.hide();
                            }
                        });
                        */
                    }
                }
            });
        });

        $DETAIL_CONT.find('a.close-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            $DETAIL_CONT.fadeOut(250, function () {
                $DETAIL_CONT_WRAP.html("");
            });
        });

        $DETAIL_CONT.find('a.arrow-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $THIS = $(this);
            $THIS.addClass('on');

            if ($THIS.hasClass('left'))
            {
                if (D_ID > 0) D_ID -= 1;
                else D_ID = dxList.length - 1;
            }
            else
            {
                if (D_ID < (dxList.length - 1)) D_ID += 1;
                else D_ID = 0;
            }

            detailContOn(D_ID);

            tlX = -1*(dxList[D_ID] - (winW * .5));
            if (tlX > 0) tlX = 0;
            if (tlX < sContainN) tlX = sContainN;

            simulateDrag(tlX, 1000);
            BoxRangeShow(tlX);

            setTimeout(function(i) {
                $THIS.removeClass('on');
            }, 175);
        });
    };
    
    return {
        init: init
    };
})(jQuery);


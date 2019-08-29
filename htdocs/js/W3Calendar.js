(function($) {
    "use strict";

    var _eventUpdater_ = undefined;
    var _eventUpdaterWrapper_ = undefined;
    
    function CalendarImpl()
    {
        var currentDate = new Date();
        
        ///////// Const /////////

        var YEAR_DATA = "setYear";
        var MONTH_DATA = "setMonth";

        var CALENDAR_HEADER_CLASS = "calendar-header";
        var CALENDAR_HEADER_TITLE_CLASS = "calendar-header-title";
        var CALENDAR_HEADER_TITLE_DATE_CLASS = "calendar-header-title-date";
        
        var CALENDAR_EVENT_LIST_CLASS = "calendar-event-list";
        var CALENDAR_EVENT_LIST_DATE_CLASS = "calendar-event-list-date";

        var M_D_CLASS = "m-d";

        var MONTH_DAY_CLASS = "month-day";
        var MONTH_DAY_BLANK_CLASS = "month-day-blank";
        var MONTH_DAY_EVENT_CLASS = "month-day-event";
        var MONTH_DAY_WRAP_CLASS = "month-day-wrap";
        var MONTH_DAY_NUMBER_CLASS = "month-day-number";
        var MONTH_DAY_INDICATOR_WRAP_CLASS = "month-day-indicator-wrap";
        var MONTH_DAY_TODAY_CLASS = "month-day-today";
        var MONTH_PAST_DAY_CLASS = "month-past-day";
        var MONTH_RESET_CLASS = "month-reset";
        var MONTH_WEEK_CLASS = "month-week";
        var MONTH_NEXT_CLASS = "month-next";
        var MONTH_PREV_CLASS = "month-prev";
        var MONTH_EVENT_LIST_ITEM_CLASS = "month-event-list-item";
        var MONTH_VIEW_BUTTON_CLASS = "month-view-button";
        
        var WEEK_TITLE_WRAP_CLASS = "week-title-wrap";
        
        ///////// Global Variable /////////

        var _uniqueId = $(this).attr("id");
        var _parent = "#" + _uniqueId;

        var _currentMonth = currentDate.getMonth() + 1;
        var _currentYear = currentDate.getFullYear();
        var _currentDay = currentDate.getDate();

        var _weekNames = _getWeekNames();
        var _monthNames = _getMonthNames();

        ///////// Init UI /////////

        _appendMonthBody();
        _addCalendarHeader();
        _appendEventList();
        _initMonth();
        _registerHandler();

        ///////// Utilities /////////

        function _getClassID(id)
        {
            return _parent + " ." + id;
        }

        function _getClassDiv(id, element)
        {
            if (element == undefined) {
                element = "";
            }

            return '<div class="' + id + '">' + element + '</div>';
        }

        function _getClassHref(id, event, element)
        {
            if (event == undefined) {
                event = "";
            } else {
                event = " " + event;
            }

            if (element == undefined) {
                element = "";
            }

            return '<a href="#" class="' + id + '"' +  event + '>' + element + '</a>';
        }

        function _getBlankDayMarkup()
        {
            return _getClassDiv(M_D_CLASS + ' ' + MONTH_DAY_BLANK_CLASS, _getClassDiv(MONTH_DAY_NUMBER_CLASS));
        }

        function _getWeekNames()
        {
            return ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        }

        function _getMonthNames()
        {
            return ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        }

        function _isLeapYear(year)
        {
            return !(year & 3) && ((year % 25) || !(year & 15))
        }

        function _getDaysOfMonth(year, month)
        {
            if (month === 2) {
                return _isLeapYear(year) ? 29 : 28;
            }
            
            return 30 + ((month + (month >> 3)) & 1);
        }

        function _isPastDay(year, month, day)
        {
            if (year < _currentYear) {
                return true;
            } else if (year > _currentYear) {
                return false;
            }

            if (month < _currentMonth) {
                return true;
            } else if (month > _currentMonth) {
                return false;
            }

            return day < _currentDay;
        }

        function _createAttr(name, value)
        {
            var attrMap = {
                "'": "&#39;",
                "\"": "&quot;",
                "<": "&lt;",
                ">": "&gt;"
            };
            
            var parseValue = String(value);
            var newValue = "";
            for (var index = 0; index < parseValue.length; index++) {
                if (parseValue[index] in attrMap) {
                    newValue += attrMap[parseValue[index]];
                } else {
                    newValue += parseValue[index];
                }
            }
            
            return " " + name + "=\"" + newValue + "\"";
        }

        function _setData(name, data)
        {
            $(_parent).data(name, data);
        }

        function _getData(name)
        {
            return $(_parent).data(name);
        }

        ///////// Element Operators /////////

        function _initMonth()
        {
            _setMonth(_currentYear, _currentMonth);
            _eventUpdaterWrapper_ = _updateEvent;
        }

        function _appendMonthBody()
        {
            var weekNameElement = "";
            _weekNames.forEach(name => {
                weekNameElement += "<div>" + name + "</div>";
            });

            $(_parent).append(_getClassDiv(WEEK_TITLE_WRAP_CLASS, weekNameElement) + _getClassDiv(MONTH_DAY_WRAP_CLASS));
        }

        function _addCalendarHeader()
        {
            $(_parent).prepend(_getClassDiv(CALENDAR_HEADER_CLASS,
                                            _getClassDiv(CALENDAR_HEADER_TITLE_CLASS, _getClassHref(CALENDAR_HEADER_TITLE_DATE_CLASS, 'onclick="return false"')) +
                                            _getClassDiv(MONTH_PREV_CLASS) +
                                            _getClassDiv(MONTH_NEXT_CLASS)));
        }
        
        function _appendEventList()
        {
            $(_parent).append(_getClassDiv(CALENDAR_EVENT_LIST_CLASS));
        }

        function _prependBlankDays(count)
        {
            var wrapperElement = $(_getClassID(MONTH_DAY_WRAP_CLASS));
            for (var index = 0; index < count; ++index) {
                wrapperElement.prepend(_getBlankDayMarkup());
            }
        }

        function _appendBlankDays(count)
        {
            var wrapperElement = $(_getClassID(MONTH_DAY_WRAP_CLASS));
            for (var index = 0; index < count; ++index) {
                wrapperElement.append(_getBlankDayMarkup());
            }
        }

        function _cleanMonth()
        {
            $(_getClassID(CALENDAR_EVENT_LIST_CLASS)).empty();
            $(_getClassID(MONTH_DAY_WRAP_CLASS)).empty();
        }

        function _fillBlankDays(year, month)
        {
            var theFirstDayOfMonth = new Date(year, month - 1, 1, 0, 0, 0, 0).getDay();
            var blankDaysAhead = theFirstDayOfMonth;
            _prependBlankDays(blankDaysAhead);

            var totalDays = blankDaysAhead + _getDaysOfMonth(year, month);
            if (totalDays % 7 !== 0) {
                var blankDaysAfter = Math.ceil((totalDays) / 7) * 7 - totalDays;
                _appendBlankDays(blankDaysAfter);
            }
        }

        function _groupDaysIntoWeeks()
        {
            var divs = $(_getClassID(M_D_CLASS));
            for (var index = 0; index < divs.length; index += 7) {
                divs.slice(index, index + 7).wrapAll(_getClassDiv(MONTH_WEEK_CLASS));
            }
        }

        function _createMonthHeader(year, month)
        {
            var isCurrentMonth = (month === _currentMonth) && (year === _currentYear);
            if (isCurrentMonth) {
                $(_parent + ' *[data-number="' + _currentDay + '"]').addClass(MONTH_DAY_TODAY_CLASS);
            }

            var resetMarkup = isCurrentMonth ? "" : _getClassHref(MONTH_RESET_CLASS);
            $(_getClassID(CALENDAR_HEADER_TITLE_CLASS)).html(_getClassHref(CALENDAR_HEADER_TITLE_DATE_CLASS,
                                                                           'onclick="return false"', 
                                                                           _monthNames[month - 1] + " " + year) +
                                                             resetMarkup);
        }

        function _createDayInMonth(year, month, day)
        {
            var innerMarkup = _getClassDiv(MONTH_DAY_NUMBER_CLASS, day) + _getClassDiv(MONTH_DAY_INDICATOR_WRAP_CLASS);
            var pastDayMarkup = _isPastDay(year, month, day) ? " " + MONTH_PAST_DAY_CLASS : "";
            var thisDate = new Date(year, month - 1, day, 0, 0, 0, 0);
            var dateStr = thisDate.toISOString().slice(0, 10);
            $(_getClassID(MONTH_DAY_WRAP_CLASS)).append("<div" +
                                                        _createAttr("class", M_D_CLASS + " " + MONTH_DAY_CLASS + " " + MONTH_DAY_EVENT_CLASS + " " + pastDayMarkup + " dt" + dateStr) + 
                                                        _createAttr("data-number", day) + ">" +
                                                        innerMarkup +
                                                        "</div>");
            $(_getClassID(CALENDAR_EVENT_LIST_CLASS)).append("<div" +
                                                             _createAttr("class", MONTH_EVENT_LIST_ITEM_CLASS) +
                                                             _createAttr("id", _uniqueId + "day" + day) +
                                                             _createAttr("data-number", day) + ">" +
                                                             _getClassDiv(CALENDAR_EVENT_LIST_DATE_CLASS, _weekNames[thisDate.getDay()] + "<br>" + day) +
                                                             "</div>");
        }

        function _createEventDetailTable(event)
        {
            var repeatNumber = Number(event["repeatmonth"]);
            var repeatStr = (repeatNumber > 1) ? ("Every " + repeatNumber + " Months") : ((repeatNumber == 0) ? "No" : "Every 1 Month");
            var eventTable = "<HR>" +
                "<table>" +
                "<tr><td style=\"float:right\">Name:</td><td>" + event["name"] + "</td></tr>" +
                "<tr><td style=\"float:right\">Start Date:</td><td>" + event["datetime"].split(" ")[0] + "</td></tr>" +
                "<tr><td style=\"float:right\">Repeat:</td><td>" + repeatStr + "</td></tr>" +
                "<tr><td style=\"float:right\">Note:</td><td>" + event["note"] + "</td></tr>" +
                "</table>";
            
            return eventTable;
        }

        function _updateSingleDayEventUI(dateNumber, events)
        {
            var dayGridEvents = "<ul>";
            var dayDetailEvents = "<div>";
            
            for (var iter in events) {
                dayGridEvents += "<li><a>" + events[iter]["name"] + "</a></li>";
                dayDetailEvents += "<div>" + _createEventDetailTable(events[iter]) + "</div>";
            }
            dayGridEvents += "</ul>";
            dayDetailEvents += "</div>";
            
            var dayGridElement = $(_parent +
                                   ' .' + MONTH_DAY_EVENT_CLASS + '[data-number="' + dateNumber + '"]' +
                                   ' .' + MONTH_DAY_INDICATOR_WRAP_CLASS);
            dayGridElement.append(dayGridEvents);

            var dayDetailElement = $(_parent + ' .' + MONTH_EVENT_LIST_ITEM_CLASS + '[data-number="' + dateNumber + '"]');
            dayDetailElement.append(dayDetailEvents);

            // Remove "No Events" CSS
            var dayDetailCSSElement = $(_parent + ' .' + MONTH_EVENT_LIST_ITEM_CLASS + '[data-number="' + dateNumber + '"]:after');
            dayDetailCSSElement.css("content", "");
        }

        function _updateCalendarEventUI(events)
        {
            var eventsDateMap = {};
            for (var iter in events) {
                // "datetime":"2019-05-01 00:00:00" and change to number to remove 0 in date string
                var dateNumber = Number(events[iter]["datetime"].split(" ")[0].split("-")[2]); 

                if (dateNumber in eventsDateMap) {
                    eventsDateMap[dateNumber].push(events[iter]);
                } else {
                    eventsDateMap[dateNumber] = [events[iter]];
                }
            }

            for (var key in eventsDateMap) {
                _updateSingleDayEventUI(key, eventsDateMap[key]);
            }
        }

        function _updateEvent(year, month)
        {
            if (_eventUpdater_ == undefined) {
                return;
            }

            if (year == undefined) {
                year = _currentYear;
            }

            if (month == undefined) {
                month = _currentMonth;
            }

            _eventUpdater_(year, month, _updateCalendarEventUI);
        }

        function _setMonth(year, month)
        {
            _setData(MONTH_DATA, month);
            _setData(YEAR_DATA, year);

            _cleanMonth();
            
            var dayNumbersInMonth = _getDaysOfMonth(year, month);
            for (var dayIter = 1; dayIter <= dayNumbersInMonth; ++dayIter) {
                _createDayInMonth(year, month, dayIter);
            }

            _createMonthHeader(year, month);
            _fillBlankDays(year, month);
            _groupDaysIntoWeeks();

            _updateEvent(year, month);
        }

        ///////// Handlers /////////
        
        function _registerHandler()
        {
            $(document.body).on("click", _getClassID(MONTH_NEXT_CLASS), function (event) {
                _hideEventList();
                _setNextMonth();
                _updateMonthViewButton();
                event.preventDefault();
            });

            $(document.body).on("click", _getClassID(MONTH_PREV_CLASS), function (event) {
                _hideEventList();
                _setPreviousMonth();
                _updateMonthViewButton();
                event.preventDefault();
            });

            $(document.body).on("click", _getClassID(MONTH_RESET_CLASS), function (event) {
                _hideEventList();
                _updateMonthViewButton();
                _setMonth(_currentYear, _currentMonth);
                event.preventDefault();
                event.stopPropagation();
            });

            $(document.body).on("click", _getClassID(MONTH_VIEW_BUTTON_CLASS), function (event) {
                _hideEventList();
                _updateMonthViewButton();
                event.preventDefault();
            });

            $(document.body).on("click touchstart", _getClassID(MONTH_DAY_CLASS), function (event) {
                // Return data-number attribute in element
                var selectedDay = $(this).data("number");

                _showEventList(selectedDay);
                _updateMonthViewButton();
                
                event.preventDefault();
            });
        }

        function _showEventList(selectedDay)
        {
            var eventListElement = $(_getClassID(CALENDAR_EVENT_LIST_CLASS));
            if (eventListElement.is(":visible")) {
                return;
            }
            
            var monthDayWrapElement = $(_getClassID(MONTH_DAY_WRAP_CLASS));
            var width = monthDayWrapElement.width();
            var height = monthDayWrapElement.height();
            var offset = monthDayWrapElement.offset();

            eventListElement.show();
            eventListElement.css("transform", "scale(1)");
            eventListElement.css("width", width);
            eventListElement.css("height", height);
            eventListElement.css("top", offset.top);
            eventListElement.css("left", offset.left);
            $(_parent + ' .' + MONTH_EVENT_LIST_ITEM_CLASS + '[data-number="' + selectedDay + '"]').show();
        }

        function _hideEventList()
        {
            var eventListElement = $(_getClassID(CALENDAR_EVENT_LIST_CLASS));
            if (!eventListElement.is(":visible")) {
                return;
            }

            eventListElement.css("transform", "scale(0)");
            eventListElement.hide();
            $(_getClassID(CALENDAR_EVENT_LIST_CLASS) + ' .' + MONTH_EVENT_LIST_ITEM_CLASS + '[style="display: block;"]').hide();
        }

        function _setNextMonth()
        {
            var month = _getData(MONTH_DATA);
            var year = _getData(YEAR_DATA);
            var newMonth = month === 12 ? 1 : month + 1;
            var newYear = month === 12 ? year + 1 : year;
            _setMonth(newYear, newMonth);
        }

        function _setPreviousMonth()
        {
            var month = _getData(MONTH_DATA);
            var year = _getData(YEAR_DATA);
            var newMonth = month === 1 ? 12 : month - 1;
            var newYear = month === 1 ? year - 1 : year;
            _setMonth(newYear, newMonth);
        }

        function _updateMonthViewButton()
        {
            $(_getClassID(MONTH_VIEW_BUTTON_CLASS)).remove();

            if ($(_getClassID(CALENDAR_EVENT_LIST_CLASS)).is(":visible")) {
                $(_getClassID(CALENDAR_HEADER_TITLE_CLASS)).prepend(_getClassHref(MONTH_VIEW_BUTTON_CLASS));
            }
        }
    }

    function CalendarEventImpl(updater)
    {
        _eventUpdater_ = updater;

        if (_eventUpdaterWrapper_ != undefined) {
            _eventUpdaterWrapper_();
        }
    }

    $.fn.extend({
        W3Calendar: CalendarImpl,
        W3CalendarEvent: CalendarEventImpl
    });
} (jQuery));

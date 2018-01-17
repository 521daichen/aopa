var iosActionSheet = {
    actsheet: '#iosActionsheet',
    title: '#iosActionsheettitle',
    listbox: '#iosActionsheetlist',
    content: function (text) {
        $(this.title).html(text);
        console.log(this.title);
    },
    list: function (arr) {
        var data = arr;
        var html = '';
        $.each(data, function (i, n) {
            html += '<div class="weui-actionsheet__cell">' + n + '</div>'
        });
        $(this.listbox).html(html);
    },
    show: function () {
        $('#iosActionsheetBox').removeClass('weui-animate-slide-down').addClass('weui-animate-slide-up');
        $('#iosMask').delay(150).fadeIn();
        $(this.actsheet).delay(150).fadeIn();


    },
    hide: function () {
        $('#iosActionsheetBox').removeClass('weui-animate-slide-up').addClass('weui-animate-slide-down');
        $('#iosMask').delay(100).fadeOut();
        $(this.actsheet).delay(100).fadeOut();
    }

};

var androidActionSheet = {
    actsheet: '#androidActionsheet',
    listbox: '#androidActionsheetlist',
    list: function (arr) {
        var data = arr;
        var html = '';
        $.each(data, function (i, n) {
            html += '<div class="weui-actionsheet__cell">' + n + '</div>'
        });
        $(this.listbox).html(html);
    },
    show: function () {
        $(this.actsheet).fadeIn();
    },
    hide: function () {
        $(this.actsheet).fadeOut();
    }

};

var loading = {
    loading: '#loadingToast',
    tip: '#loadingContent',
    content: function (title) {
        $(this.tip).html(title);
    },
    show: function () {
        $(this.loading).css('display','block');
    },
    hide: function () {
        $(this.loading).css('display','none');
    }
};
var toast = {
    toast: '#toast',
    tip: '#toastContent',
    content: function (title) {
        $(this.tip).html(title);
    },
    show: function () {
        $(this.toast).fadeIn();
    },
    hide: function () {
        $(this.toast).fadeOut();
    }
};
var dialogBtns = {
    dialog: '#dialogBtns',
    title: '#dialogBtnstitle',
    content: '#dialogBtnsContent',
    firstOp: '#firstOp',
    secondOp: '#secondOp',
    show: function () {
        $(this.dialog).fadeIn();
    },
    hide: function () {
        $(this.dialog).fadeOut();
    },
    setTitle: function (text) {
        $(this.title).html(text);
    },
    setContent: function (text) {
        $(this.content).html(text);
    },
    setfirstOp: function (text, fn) {
        $(this.firstOp).off();
        $(this.firstOp).html(text);
        $(this.firstOp).on('click', fn);
    },
    setsecondOp: function (text, fn) {
        $(this.secondOp).off();
        $(this.secondOp).html(text);
        $(this.secondOp).on('click', fn);
    }

}

var dialogBtn = {
    dialog: '#dialogBtn',
    content: '#dialogBtnContent',
    operate: '#operate',
    show: function () {
        $(this.dialog).css('display','block');
    },
    hide: function () {
        $(this.dialog).css('display','none');
    },
    setContent: function (text) {
        $(this.content).html(text);
    },
    setOperate: function (text, fn) {
        $(this.operate).off();
        $(this.operate).html(text);
        $(this.operate).on('click', fn);
    }

}
var simPicker = {
    picker: '#simPicker',
    content: function (obj) {
        var option={};
        var options=[];
        $.each(obj,function(i,n){
           options.push({label:i,value:n});
        });
        weui.picker(options,
            {
                onChange: function (result) {
                   console.log(result);
                },
                onConfirm: function (result) {
                    console.log(result);
                }
            });
    }
};
var datePicker = {
    picker: '#datePicker',
    content: function (year) {
        var startyear=year;
        weui.datePicker({
            start: startyear,
            end: new Date().getFullYear(),
            onChange: function (result) {
                console.log(result);
            },
            onConfirm: function (result) {
                console.log(result);
            }
        });
    }
};

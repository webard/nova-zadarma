var zdrmWebrtcPhone;
var zadarmaWidgetFn = function (hash, sip, shape /*square|circle*/, lang /*ru, en, es, fr, de, pl, ua*/, fixed /*true,false*/, position) {
    var zdrmSipIsTrue = false;
    var noTransferZdrm;
    var sipsToTransfer = [];
    var browserNotSupported = false;
    var texts = [];
    var options = {
        type: 'site',
        key: hash,
        sip: sip,
        language: lang,
        fixed: fixed,

        position: position,
        form: shape,
        getSipsCallback: function (sips, errorCode, errorText) {
            var re;
            if(sips.disabled && sips.disabled === true){
                document.getElementsByClassName('zdrm-phone')[0].className = document.getElementsByClassName('zdrm-phone')[0].className + ' zdrm-webphone-hide';

                document.getElementsByClassName('zdrm-webrtc-error')[0].className = document.getElementsByClassName('zdrm-webrtc-error')[0].className.replace('zdrm-webphone-hide', '');
                document.getElementsByClassName('zdrm-webrtc-error')[0].innerHTML = errorCode + ': ' + errorText;
                return false;
            }

            if (options.sip.search('-') > -1) {
                re = new RegExp(options.sip, 'ig');
            } else{
                re = new RegExp('[0-9]+-' + options.sip, 'ig');
            }
            for (var i = 0; i < sips.pbx.length; i++) {
                if (sips.pbx[i].name.match(re)) {
                    zdrmSipIsTrue = true;
                    noTransferZdrm = false;
                }else{
                    sipsToTransfer[sipsToTransfer.length] = sips.pbx[i].name;
                }
            }

            if(noTransferZdrm === false) {
                var sipsToTransferText = '';
                var un = '';
                for (var k in sipsToTransfer) {
                    if(un = sipsToTransfer[k].match(/^[0-9]+-([0-9]+)$/i)) {
                        sipsToTransferText += '<option value="' + un[1] + '">' + un[1] + '</option>';
                    }
                }

                document.getElementsByClassName('zdrm-webphone-number-selector')[0].innerHTML = sipsToTransferText;
            }else{
                document.getElementsByClassName('zdrm-webphone-redirect-button')[0].style.display = 'none';
            }
            if (zdrmSipIsTrue === false) {
                var re = new RegExp(options.sip, 'ig');
                for (var i = 0; i < sips.sip.length; i++) {
                    if (sips.sip[i].name.match(re)) {
                        zdrmSipIsTrue = true;
                        noTransferZdrm = true;
                        break;
                    }
                }
            }
        },

        getStatusMessage: function (msg, parameters) {
            console.log('LOOOL',msg, parameters);
            if (msg == 'browserNotSupported') {

            } else if (msg == 'canceled' || msg == 'rejected' || msg == 'busy') {
                zdrmWPhI.finishCall();
                zdrmWPhI.hideCancelBtn();
                document.getElementsByClassName('zdrm-webphone-callername')[0].innerHTML = '';
                document.getElementById('zdrm-webphone-phonenumber-input').classList.remove('incoming');

                Nova.$emit('end-phone-call', {
                    status: msg,
                    parameters: parameters
                });
            } else if (msg == 'confirmed') {
                zdrmWPhI.startTimer();
                zdrmWPhI.hideCancelBtn();
                document.getElementsByClassName('zdrm-webphone-media-box')[0].className = document.getElementsByClassName('zdrm-webphone-media-box')[0].className.replace(/zdrm-webphone-hide/ig, '');
            } else if (msg == 'incoming') {
                zdrmWPhI.status = 'incoming';
                if(parameters.callername){
                    document.getElementsByClassName('zdrm-webphone-callername')[0].innerHTML = parameters.callername;
                }
                zdrmWPhI.setCallingNumber(parameters.caller);
                zdrmWPhI.ringing();
                zdrmWPhI.showCancelBtn();
                document.getElementById('zdrm-webphone-phonenumber-input').classList.add('incoming');

                Nova.$emit('zadarma-incoming-phone-call', {
                    msg: msg,
                    caller: parameters.caller,
                    callerName: parameters.callerName ?? null,
                    callerDid: parameters.callerDid ?? null
                });

            } else if (msg == 'outgoing') {
                zdrmWPhI.setCallingNumber(parameters.dst);
                document.getElementsByClassName('zdrm-webphone-callername')[0].innerHTML = '';
                document.getElementById('zdrm-webphone-phonenumber-input').classList.remove('incoming');
            }
        },
        callbackGetPrice: function (data) {
            if (data.iso) {
                document.getElementsByClassName('zdrm-webphone-direction')[0].innerHTML = '' +
                '<div class="zdrm-webphone-direction-flag zdrm-webphone-direction-flag-' + data.iso + '"></div> ' +
                    data.title + ' - ' + data.cost + ' ' + data.sign + (zdrmWPhI && zdrmWPhI.texts && zdrmWPhI.texts.minute ? zdrmWPhI.texts.minute : (zdrmWPhI.apiWidget && zdrmWPhI.apiWidget.texts && zdrmWPhI.apiWidget.texts.minute ? zdrmWPhI.apiWidget.texts.minute : '/min.'));
            } else {
                document.getElementsByClassName('zdrm-webphone-direction')[0].innerHTML = '';
            }
        },
        callbackEndCall: function (response) {
            Nova.$emit('end-phone-call', response);
            zdrmWPhI.finishCall();

        },
        labelsCallbackFn: function(_texts){
            document.getElementById('transfer_label').innerHTML = _texts.transfer ? _texts.transfer : 'Transfer <span' +
                ' class="zdrm-redirect-hide" style="display:none;">to</span>';

            document.getElementsByClassName('zdrm-webphone-phonenumber-container')[0].getElementsByTagName('input')[0].setAttribute('placeholder', _texts.ENTER_PHONE_NUMBER ? _texts.ENTER_PHONE_NUMBER : 'Enter number');

            document.getElementsByClassName('zdrm-webphone-placeholder-double')[0].innerHTML = _texts.ENTER_PHONE_NUMBER ? _texts.ENTER_PHONE_NUMBER : 'Enter number';

            document.getElementsByClassName('zdrm-webphone-ext-number')[0].onclick = function(){
                zdrmWPhI.apiWidget.transfer(document.getElementsByClassName('zdrm-webphone-number-selector')[0].value, 'blind');

                document.getElementsByClassName('zdrm-webphone-redirected-text')[0].innerHTML =
                    zdrmWPhI.apiWidget.texts.call_transfered_to ?
                        zdrmWPhI.apiWidget.texts.call_transfered_to :
                        'Звонок переведен на';

                document.getElementsByClassName('zdrm-webphone-redirected-number')[0].innerHTML =
                    document.getElementsByClassName('zdrm-webphone-number-selector')[0].value;
            };
        }
    };
    zdrmWebrtcPhone = new zdrmWebrtcPhoneInterface(options);
};

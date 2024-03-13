export default {
    // saveFeedback(feedback, rating, conclusion) {
    //     return Nova.request()
    //       .post(`/nova-vendor/webard/nova-zadarma/get-phone-number-info`)
    //       .then((response) => response.data);
    //   },

    getPhoneNumberInfo(phoneNumber) {
        return Nova.request()
          .post(`/nova-vendor/webard/nova-zadarma/get-phone-number-info`, {
            'phoneNumber': phoneNumber,
            'lol': 'lol',
          })
          .then((response) => response.data);
    }
};

export default {
    saveFeedback(feedback, rating, conclusion) {
        return Nova.request()
          .post(`/nova-vendor/KABBOUCHI/logs-tool/logs?file=${file}`)
          .then((response) => response.data);
      },
};

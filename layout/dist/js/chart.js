let chartManager = {
  'restartDonut': (donutObject, data)=>{
    let labels = [];
    let values = [];
    let backgroundColor = [];
    let donutTempData = {};
    
    donutObject.data.labels.pop();
    donutObject.data.datasets.forEach((dataset) => {
      dataset.data.pop();
    });
    donutObject.update();

    for(item in data.resultado){
      labels.push(data.resultado[item].opcao);
      values.push(data.resultado[item].votos);
      backgroundColor.push('#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0'));
    }

    donutTempData.labels = labels;
    donutTempData.datasets = [{data:values, backgroundColor:backgroundColor}];
    
    donutObject.data = donutTempData;
    donutObject.update();
  },

  'startDonut':(donutCanvasId, donutOptions,type)=>{
    let donutChartCanvas = $('#'+donutCanvasId).get(0).getContext('2d');
    let initialDonutData = {
      labels: [
          'Dados não carregados'
      ],
      datasets: [
        {
          data: [100],
        }
      ]
    }

    donutChart = new Chart(donutChartCanvas, {
      type: type,
      data: initialDonutData,
      options: donutOptions      
    });

    return donutChart;
  }
}
let chartScripts =  (($) => {
    
    var donutChartP1;
    var donutChartP2;
    let host = 'http://'+window.location.host;
    let donutData2 = {};
    let donutChart;

    let loadSelectPesquisa = () => {
      $.get(host+'/loadSelectPesquisas.php', function(data){
        let html = "<option>Selecione...</option>";
        for(item in data){
          html+= "<option value="+data[item].identificadorPesquisa+">"+data[item].descricao+"</option>";
        }

        $(".selectPesquisa").html(html);
      },'json'); 
    }

    let listenSelectPesquisaGeral = () => {
      $(".selectPesquisa").on('change',function(event){
        let tipoPesquisa = $(this).data('pesquisa');
        let identificadorPesquisa = event.target.value;
        let html = "<option>Selecione...</option>";

        $.get(host+'/loadSelectPesquisaQuestoes.php?identificadorPesquisa='+identificadorPesquisa,function(data){
          for(item in data){
            html+= "<option value="+data[item]._id.$oid+">"+data[item].textoQuestao+"</option>";
          }
          
          $("#selectQuestao"+tipoPesquisa).html(html);
          $("#titleQuestao"+tipoPesquisa).text("Exibindo: " +event.target[event.target.selectedIndex].text);

          $('.select2').select2();

        }, 'json');
      });
    }

    let listenSelectPesquisaLocalidade = () => {
      
      $(".selectPesquisa").on('change',function(event){
        let tipoPesquisa = $(this).data('pesquisa');
        let identificadorPesquisa = event.target.value;
        let html = "<option>Selecione...</option>";

        $.get(host+'/loadSelectPesquisaBairro.php?identificadorPesquisa='+identificadorPesquisa,function(data){
          let html = "<option>Selecione...</option>";
          for(item in data){
            html+= "<option value="+data[item]._id.$oid+">"+data[item].bairro+"</option>";
          }

          $("#selectBairro"+tipoPesquisa).html(html);
          $("#titleQuestao"+tipoPesquisa).text("Exibindo: " + event.target[event.target.selectedIndex].text);

        },'json');
      })
    }

    let loadSelectQuestao = (donutChartP1,donutChartP2) => {
      $(".selectQuestao").on('change', function(event){
        let tipoPesquisa = $(this).data('pesquisa');
        let questaoID = event.target.value;

        $("#textoQuestao"+tipoPesquisa).text(event.target.selectedOptions[0].text);

        $.get(host+'/loadQuestaoData.php?id='+questaoID,'json')
        .then(function(data){
          $("#entrevistadosQuestao"+tipoPesquisa).text("Entrevistados: " + data.entrevistados);          
          switch(tipoPesquisa){
            case 'P1':
              chartManager.restartDonut(donutChartP1,data);
            break;
            case 'P2':
              chartManager.restartDonut(donutChartP2,data);
            break;
          }        
        }.bind(this)) 
      });
    }

    let loadSelectQuestaoBairro = (donutChartP1, donutChartP2) => {
      $(".selectBairro").on('change', function(event){
        let html = "";
        let bairroID = event.target.value;
        let tipoPesquisa = $(this).data('pesquisa');

        $.get(host+'/loadSelectQuestaoBairro.php?id='+bairroID,'json')
        .then(function(data){
          html+="<option>Selecione...</option>";
          for (item in data.questoes){
            html+= "<option value="+data.questoes[item].numeroQuestao+">"+data.questoes[item].textoQuestao+"</option>";
          }

          $("#selectQuestao"+tipoPesquisa).html(html);       
          sessionStorage.setItem('questoes'+tipoPesquisa,JSON.stringify(data.questoes));
        });
      });

      $(".selectQuestao").on('change', function(event){
        let tipoPesquisa = $(this).data('pesquisa');                   
        let questaoID = event.target.value;
        let questoes = JSON.parse(sessionStorage.getItem('questoes'+tipoPesquisa));
        let questaoSelecionada = questoes[questaoID];
        
        $("#textoQuestao"+tipoPesquisa).text(event.target.selectedOptions[0].text);
        $("#entrevistadosQuestao"+tipoPesquisa).text('Entrevistados: '+ questaoSelecionada.entrevistados);
        
        switch(tipoPesquisa){
          case 'P1':
            chartManager.restartDonut(donutChartP1,questaoSelecionada);
          break;
          case 'P2':
            chartManager.restartDonut(donutChartP2,questaoSelecionada);
          break;
        }
      });
    }

    return {
      loadSelectPesquisa:loadSelectPesquisa,
      loadSelectQuestao: loadSelectQuestao,
      listenSelectPesquisaGeral:listenSelectPesquisaGeral,
      listenSelectPesquisaLocalidade:listenSelectPesquisaLocalidade,
      loadSelectQuestaoBairro:loadSelectQuestaoBairro
    }
})($)
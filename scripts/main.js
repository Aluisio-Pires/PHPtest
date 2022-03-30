function xmlToJson(xml) {
    var obj = {}, i, j, attribute, item, nodeName, old;

    if (xml.nodeType === 1) { // element
        // do attributes
        if (xml.attributes.length > 0) {
            obj["@attributes"] = {};
            for (j = 0; j < xml.attributes.length; j = j + 1) {
                attribute = xml.attributes.item(j);
                obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
            }
        }
    } else if (xml.nodeType === 3) { // text
        obj = xml.nodeValue.trim();
    }

    // do children
    if (xml.hasChildNodes()) {
        for (i = 0; i < xml.childNodes.length; i = i + 1) {
            item = xml.childNodes.item(i);
            nodeName = item.nodeName;
            if ((obj[nodeName]) === undefined) {
                obj[nodeName] = xmlToJson(item);
            } else {
                if ((obj[nodeName].push) === undefined) {
                    old = obj[nodeName];
                    obj[nodeName] = [];
                    obj[nodeName].push(old);
                }
                obj[nodeName].push(xmlToJson(item));
            }
        }
    }
    return obj;
}

function parseXml(xml, arrayTags) {
    let dom = null;
    if (window.DOMParser) {
        dom = (new DOMParser()).parseFromString(xml, "text/xml");
    } else if (window.ActiveXObject) {
        dom = new ActiveXObject('Microsoft.XMLDOM');
        dom.async = false;
        if (!dom.loadXML(xml)) {
            throw dom.parseError.reason + " " + dom.parseError.srcText;
        }
    } else {
        throw "Erro ao conveter XML!";
    }

    function isArray(o) {
        return Object.prototype.toString.apply(o) === '[object Array]';
    }

    function parseNode(xmlNode, result) {
        if (xmlNode.nodeName === "#text") {
            const v = xmlNode.nodeValue;
            if (v.trim()) {
                result['#text'] = v;
            }
            return;
        }

        let jsonNode = {};
        let existing = result[xmlNode.nodeName];
        if (existing) {
            if (!isArray(existing)) {
                result[xmlNode.nodeName] = [existing, jsonNode];
            } else {
                result[xmlNode.nodeName].push(jsonNode);
            }
        } else {
            if (arrayTags && arrayTags.indexOf(xmlNode.nodeName) !== -1) {
                result[xmlNode.nodeName] = [jsonNode];
            } else {
                result[xmlNode.nodeName] = jsonNode;
            }
        }

        let length;
        if (xmlNode.attributes) {
            length = xmlNode.attributes.length;
            for (let i = 0; i < length; i++) {
                const attribute = xmlNode.attributes[i];
                jsonNode[attribute.nodeName] = attribute.nodeValue;
            }
        }

        length = xmlNode.childNodes.length;
        for (let i = 0; i < length; i++) {
            parseNode(xmlNode.childNodes[i], jsonNode);
        }
    }

    let result = {};
    for (let i = 0; i < dom.childNodes.length; i++) {
        parseNode(dom.childNodes[i], result);
    }

    return result;
}

function httpGet(url) {
    const xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);
    return xmlHttp.responseText;
}

function getDadosEnderecoPorCEP(cep) {
    let url = 'https://viacep.com.br/ws/'+cep+'/xml/'

    let xmlHttp = new XMLHttpRequest()
    xmlHttp.open('GET', url)

    xmlHttp.onreadystatechange = () => {
        if(xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            let dadosXMLText = xmlHttp.responseText

            let parser = new DOMParser()
            domXML = parser.parseFromString(dadosXMLText, 'text/xml')
            let dadosJSONObj = xmlToJson(domXML)

            console.log(dadosJSONObj['xmlcep'])
            if(dadosJSONObj['xmlcep']['erro']){
                document.getElementById('erro').innerText = "CEP inválido"
            }

            document.getElementById('logradouro').value = dadosJSONObj['xmlcep']['logradouro']['#text'] ? dadosJSONObj['xmlcep']['logradouro']['#text'] : null
            document.getElementById('bairro').value = dadosJSONObj['xmlcep']['bairro']['#text'] ? dadosJSONObj['xmlcep']['bairro']['#text'] : null
            document.getElementById('cidade').value = dadosJSONObj['xmlcep']['localidade']['#text'] ? dadosJSONObj['xmlcep']['localidade']['#text'] : null
            document.getElementById('uf').value = dadosJSONObj['xmlcep']['uf']['#text'] ? dadosJSONObj['xmlcep']['uf']['#text'] : null
            
        }
    }

    xmlHttp.send()
}

function verificaDados(cep){
    let cepInteiro = cep.replace(/,/g, "").replace(/\./g, "").replace(/[^0-9]/g,'')
    document.getElementById('logradouro').value = null
    document.getElementById('bairro').value = null
    document.getElementById('cidade').value = null
    document.getElementById('uf').value = null
    document.getElementById('erro').innerText = null
    if(cepInteiro.length !== 8){
        document.getElementById('erro').innerText = "CEP deve possuir 8 dígitos"
    }
    else{
        let req = httpGet("busca.php?cep=" + cep);

        let res = parseXml(req).xmlcep;

        if (res === undefined || res.hasOwnProperty("erro")) {
            document.getElementById('erro').innerText = "CEP não encontrado"
            return true
        }
        document.getElementById('logradouro').value = res['logradouro']['#text'] ? res['logradouro']['#text'] : null
        document.getElementById('bairro').value = res['bairro']['#text'] ? res['bairro']['#text'] : null
        document.getElementById('cidade').value = res['localidade']['#text'] ? res['localidade']['#text'] : null
        document.getElementById('uf').value = res['uf']['#text'] ? res['uf']['#text'] : null
        //getDadosEnderecoPorCEP(cepInteiro)
    }

}
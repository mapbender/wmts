Mapbender.Geo.TmsSourceHandler = Class({'extends': Mapbender.Geo.SourceHandler },{
    'private object defaultOptions': {
        
    },
    'private string layerNameIdent': 'identifier',
    'public function getDefaultOptions': function() {
        return this.defaultOptions();
    },
    'public function setDefaultOptions': function() {
        return this.defaultOptions();
    },
    'public function create': function(sourceOpts) {
        var self = this;
        // TODO
        var rootLayer = sourceOpts.configuration.children[0];
//                if(sourceDef.configuration.status !== 'ok'){ //deactivate corrupte or unreachable sources
//                    rootLayer.options.treeOptions.selected = false;
//                    rootLayer.options.treeOptions.allow.selected = false;
//                }
        function _setProperties(layer, parent, id, num, proxy){
            /* set unic id for a layer */
            layer.options.origId = layer.options.id;
            layer.options.id = parent ? parent.options.id + "_" + num : id + "_" + num;
            if(proxy && layer.options.legend) {
                if(layer.options.legend.graphic) {
                    layer.options.legend.graphic = Mapbender.Util.addProxy(layer.options.legend.graphic);
                } else if(layer.options.legend.url) {
                    layer.options.legend.url = Mapbender.Util.addProxy(layer.options.legend.url);
                }
            }
            if(layer.children) {
                for(var i = 0; i < layer.children.length; i++) {
                    _setProperties(layer.children[i], layer, id, i, proxy);
                }
            }
        }
        _setProperties(rootLayer, null, sourceOpts.id, 0, sourceOpts.configuration.options.proxy);

        var proj = Mapbender.Model.getCurrentProj();
        var layer = this.findLayerEpsg(sourceOpts.configuration.layers,
            sourceOpts.configuration.tilematrixsets, proj.projCode, true);
        if (!layer) { // find first layer with epsg from srs list to initialize.
            var allsrs = Mapbender.Model.getAllSrs();
            for (var i = 0; i < allsrs.length; i++) {
                layer = this.findLayerEpsg(sourceOpts.configuration.layers,
                    sourceOpts.configuration.tilematrixsets, allsrs[i].name, true);
                if (layer) {
                    break;
                }
            }
            // TODO disable layer 
        }
        rootLayer['children'] = [layer];

        var layerOptions = this.createLayerOptions(layer, sourceOpts.configuration.tilematrixsets, proj,
            sourceOpts.configuration.options.proxy, null);
        
//        layerOptions = {
//            label: 'leck mich ...',
//            tileOrigin: OpenLayers.LonLat.fromArray([200000.0,5880000.0]),
//            tileSize: new OpenLayers.Size(256,256),
//            url: 'http://geo.sv.rostock.de/geodienste/luftbild/tms/',
//            layer: 'luftbild/EPSG25833',
//            format: 'png'
//            
//        }
        layerOptions['url'] = 'http://geo.sv.rostock.de/geodienste/luftbild/tms/';
        layerOptions['layer'] = 'luftbild/EPSG25833';
        var mqLayerDef = {
            type: 'tms',
            isBaseLayer: false,
            opacity: sourceOpts.configuration.options.opacity,
            visible: sourceOpts.configuration.options.visible,
            attribution: sourceOpts.configuration.options.attribution
        };
        $.extend(layerOptions, mqLayerDef, this.defaultOptions);
        
        
        return layerOptions;
    },
    'private function createLayerOptions': function(layer, matrixsets, projection, proxy, olLayer){
        var matrixset = this.findMatrixSetIdent(matrixsets, layer.options.tilematrixset, null, true);
        var tileFullExtent = null;
        if(layer.options.bbox[matrixset.supportedCrs]){
            tileFullExtent =
                OpenLayers.Bounds.fromArray(layer.options.bbox[matrixset.supportedCrs]);
        } else {
            for(srs in layer.options.bbox){
                tileFullExtent = OpenLayers.Bounds.fromArray(layer.options.bbox[srs]).transform(
                    Mapbender.Model.getProj(srs),
                    Mapbender.Model.getProj(matrixset.supportedCrs)
                );
                break;
            }
        }
        var layerOptions = {
            label: layer.options.title,
            layer: layer.options.identifier,
            format: layer.format_ext,
//            type: layer.type,
//            style: layer.options.style,
//            matrixSet: matrixset.identifier,
//            matrixIds: matrixset.tilematrices,
            tileOrigin: OpenLayers.LonLat.fromArray(matrixset.origin),
            tileSize: new OpenLayers.Size(matrixset.tileSize[0], matrixset.tileSize[1]),
//            tileFullExtent: tileFullExtent,
            url: proxy ? Mapbender.Util.addProxy(matrixset.url) : matrixset.url
        };
        if(olLayer){
            layerOptions['format'] = olLayer.format === layer.options.format ? olLayer.format : layer.options.format;
            layerOptions['formatSuffix'] = olLayer.format === layer.options.format ? olLayer.formatSuffix
                    : layer.options.format.substring(layer.options.format.indexOf('/') + 1);
            layerOptions['params'] = {LAYERS: [layer.options.identifier]};
        }
        return layerOptions;
    },
    'private function findLayerEpsg': function(layers, matrixSets, epsg, clone){
        var matrixSet = this.findMatrixSetEpsg(matrixSets, epsg, clone);
        for (var i = 0; matrixSet && i < layers.length; i++) {
            if(matrixSet.identifier === layers[i].options.tilematrixset){
                return clone ? $.extend(true, {}, layers[i]) : layers[i];
            }
        }
        return null;
    },
    'private function findMatrixSetEpsg': function(matrixSets, epsg, clone){
        for(var i = 0; i < matrixSets.length; i++){
            if(epsg === matrixSets[i].supportedCrs){
                return clone ? $.extend(true, {}, matrixSets[i]) : matrixSets[i];
            }
        }
        return null;
    },
    'private function findMatrixSetIdent': function(matrixSets, identifier, clone){
        for(var i = 0; i < matrixSets.length; i++){
            if(identifier === matrixSets[i].identifier){
                return clone ? $.extend(true, {}, matrixSets[i]) : matrixSets[i];
            }
        }
        return null;
    },
    'private function findMatrix': function(matrices, identifier, clone){
        for (var i = 0; i < matrices.length; i++) {
            if(identifier === matrices[i].identifier){
                return clone ? $.extend(true, {}, matrices[i]) : matrices[i];
            }
        }
        return null;
    },
    'public function onLoadStart': function(source) {
        this.enable(source, 'loadError');
    },
    'public function onLoadError': function(imgEl, sourceId, projection, callback) {
        this['super']('onLoadError', imgEl, sourceId, projection, callback);
    },
    'private function enable': function(source, tagname) {
        var sourceIdx = {
            id: source.id
        };
        var options = {
            layers: {}
        };
        if (source.configuration.children[0][tagname]) {
            options.layers[source.configuration.children[0].options.id] = {
                options: {
                    treeOptions: {
                        selected: source.configuration.children[0][tagname].selected,
                        allow: {
                            selected: source.configuration.children[0][tagname].allow.selected
                        }
                    }
                }
            };
            delete(source.configuration.children[0][tagname]);
            var toChangeOptions = this.createOptionsLayerState(source, options, false, true);
            Mapbender.Model.changeSource(toChangeOptions);
        }
    },
    'private function disable': function(source, tagname) {
        source.configuration.children[0][tagname] = {
            selected: source.configuration.children[0].options.treeOptions.selected,
            allow: {
                selected: source.configuration.children[0].options.treeOptions.allow.selected
            }
        };
        var options = {
            layers: {}
        };
        options.layers[source.configuration.children[0].options.id] = {
            options: {
                treeOptions: {
                    selected: false,
                    allow: {
                        selected: false
                    }
                }
            }
        };
        var toChangeOptions = this.createOptionsLayerState(source, options, false, true);
        Mapbender.Model.changeSource(toChangeOptions);
    },
    'public function featureInfoUrl': function(mqLayer, x, y) {
        
    },
    'public function createSourceDefinitions': function(xml, options) {
        // TODO ???
    },
    'public function getPrintConfig': function(layer, bounds, isProxy) {
        var printConfig = {
            type: 'tms',
            url: isProxy ? Mapbender.Util.removeProxy(layer.getURL(bounds)) : layer.getURL(bounds)
        };
        return printConfig;
    },
    'public function changeProjection': function(source, projection) {
        var layer = this.findLayerEpsg(source.configuration.layers,
            source.configuration.tilematrixsets, projection.projCode, true);
        if(layer){
            var mqLayer = Mapbender.Model.getMqLayer(source);
            var layerOptions = this.createLayerOptions(layer, source.configuration.tilematrixsets, projection,
            source.configuration.options.proxy, mqLayer.olLayer);
            $.extend(mqLayer.olLayer, layerOptions);
            mqLayer.olLayer.updateMatrixProperties();
            this.enable(source, 'nosrs');
        } else {// deactivate layer
            this.disable(source, 'nosrs');
        }
        
    }
});
Mapbender.source['tms'] = new Mapbender.Geo.TmsSourceHandler();

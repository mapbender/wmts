Mapbender.Geo.WmtsSource = Class({'extends': Mapbender.Geo.Source },{
    'private object defaultOptions': {
        type: 'wmts'
    },
    'private string layerNameIdent': 'identifier',
    getDefaultOptions: function() {
        return this.defaultOptions();
    },
    setDefaultOptions: function() {
        return this.defaultOptions();
    },
    create: function(sourceOpts) {
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
                    layer.options.legend.graphic = self._addProxy(layer.options.legend.graphic);
                } else if(layer.options.legend.url) {
                    layer.options.legend.url = self._addProxy(layer.options.legend.url);
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
        var layer = this._getLayer(sourceOpts.configuration.layers,
            sourceOpts.configuration.tilematrixsets, proj.projCode.toUpperCase(), true);
        if (!layer) {
            var allsrs = Mapbender.Model.getAllSrs();
            for(var i = 0; i < allsrs.length; i++){
                    layer = this._getLayer(sourceOpts.configuration.layers,
                        sourceOpts.configuration.tilematrixsets, allsrs[i].name, true);
                if(layer) {
                    break;
                }
            }
            // TODO disable layer 
        }
        rootLayer['children'] = [layer];
        var tilematrixset = this._getTileMatrixSet(sourceOpts.configuration.tilematrixsets,
            layer.options.tilematrixset, null, true);

        var tileFullExtent = null;
        if(layer.options.bbox[tilematrixset.supportedCrs.toUpperCase()]){
            tileFullExtent =
                OpenLayers.Bounds.fromArray(layer.options.bbox[tilematrixset.supportedCrs.toUpperCase()]);
        } else {
            for(srs in layer.options.bbox){
                tileFullExtent = OpenLayers.Bounds.fromArray(layer.options.bbox[srs]).transform(
                    Mapbender.Model.getProj(srs.toUpperCase()),
                    Mapbender.Model.getProj(tilematrixset.supportedCrs.toUpperCase())
                );
                break;
            }
        }
        var mqLayerDef = {
            type: 'wmts',
            label: sourceOpts.configuration.title,
            url: sourceOpts.configuration.options.proxy ? this._addProxy(layer.options.url) : layer.options.url,
            layer: layer.options.identifier,//sourceDef.configuration.layer,
            style: layer.options.style,
            matrixSet: tilematrixset.identifier,//layer.options.matrixSet,
            matrixIds: tilematrixset.tilematrixes,
            format: layer.options.format,
            tileOrigin: OpenLayers.LonLat.fromArray(tilematrixset.origin),
            tileSize: new OpenLayers.Size(tilematrixset.tileSize[0], tilematrixset.tileSize[1]),
            tileFullExtent: tileFullExtent,
            isBaseLayer: false,
            opacity: sourceOpts.configuration.options.opacity,
            visible: sourceOpts.configuration.options.visible,
            attribution: sourceOpts.configuration.options.attribution
        };
        $.extend(mqLayerDef, this.defaultOptions);
        return mqLayerDef;
    },
    _getLayer: function(layers, tileMatrixSets, epsg, clone){
        for (var i = 0; i < layers.length; i++) {
            var tms = this._getTileMatrixSet(tileMatrixSets, layers[i].options.tilematrixset, epsg, clone);
            if(tms){
                return clone ? $.extend(true, {}, layers[i]) : layers[i];
            }
        }
        return null;
    },
    _getTileMatrixSet: function(tileMatrixSets, identifier, epsg, clone){
        for(var i = 0; i < tileMatrixSets.length; i++){
            if(tileMatrixSets[i].identifier === identifier){
                if(epsg && epsg.toUpperCase() === tileMatrixSets[i].supportedCrs.toUpperCase()){
                    return clone ? $.extend(true, {}, tileMatrixSets[i]) : tileMatrixSets[i];
                } else if(!epsg) {
                    return clone ? $.extend(true, {}, tileMatrixSets[i]) : tileMatrixSets[i];
                } else {
                    return null;
                }
            }
        }
        return null;
    },
    featureInfoUrl: function(mqLayer, x, y) {
        // TODO
    },
    createSourceDefinitions: function(xml, options) {
        // TODO 
    },
    getPrintConfig: function(layer, bounds, isProxy) {
        var printConfig = {
            type: 'wmts',
            url: isProxy ? this._removeProxy(layer.getURL(bounds)) : layer.getURL(bounds)
        };
        return printConfig;
    },
    onLoadError: function(imgEl, sourceId, projection, callback) {
        //TODO
    }
});
Mapbender.source['wmts'] = new Mapbender.Geo.WmtsSource();

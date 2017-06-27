function ckstyle_finecms() {

    var ck = {

        cpath: '',

        language: '',

        flashvars: '',

        setup: '1,1,1,1,1,2,0,1,2,0,0,1,200,0,2,1,0,1,1,1,2,10,3,0,1,2,3000,0,0,0,0,1,1,1,1,1,1,250,0,90,0,0',
        pm_bg: '0x000000,100,230,180',

        mylogo: 'logo.swf',

        pm_mylogo: '1,1,-100,-55',

        logo: 'null',

        pm_logo: '2,0,-100,20',

        control_rel: 'related.swf,ckplayer/related.xml,0',

        control_pv: 'Preview.swf,105,2000',

        pm_repc: '',

        pm_spac: '|',

        pm_fpac: 'file->f',

        pm_advtime: '2,0,-110,10,0,300,0',

        pm_advstatus: '1,2,2,-200,-40',

        pm_advjp: '1,1,2,2,-100,-40',

        pm_padvc: '2,0,-10,-10',

        pm_advms: '2,2,-46,-56',

        pm_zip: '1,1,-20,-8,1,0,0',

        pm_advmarquee: '1,2,50,-60,50,20,0,0x000000,50,0,20,1,30,2000',

        pm_glowfilter:'1,0x01485d, 100, 6, 3, 10, 1, 0, 0',

        advmarquee: escape('{text}'),

        mainfuntion:'',

        flashplayer:'',

        calljs:'ckplayer_status,ckadjump,playerstop,ckmarqueeadv',

        myweb: escape(''),

        cpt_lights: '1',

        cpt_share: '{api_url}&at=share',

        cpt_list: ckcpt()

    }

    return ck;

}
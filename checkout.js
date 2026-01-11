const settings = window.wc.wcSettings.getSetting( 'eps_data', {} );
const { createElement } = window.wp.element;
const { decodeEntities } = window.wp.htmlEntities;
const { __ } = window.wp.i18n;

const labelText = decodeEntities( settings.title ) || __( 'EPS', 'eps' );

// Description with HTML (banner, etc.)
const Content = () => {
    return createElement( 'div', {
        dangerouslySetInnerHTML: { __html: settings.description || '' }
    } );
};

// ✅ Icon FIRST, then title
const Label = () => {
    return createElement(
        'span',
        { style: { display: 'inline-flex', alignItems: 'center' } },
        settings.icon &&
            createElement( 'img', {
                src: settings.icon,
                alt: 'EPS Logo',
                style: { height: '24px', marginRight: '8px' } // icon on left
            } ),
        createElement( 'span', null, labelText )
    );
};

const Block_Gateway = {
    name: 'eps',
    label: createElement( Label ),
    content: createElement( Content ),
    edit: createElement( Content ),
    canMakePayment: () => true,
    ariaLabel: labelText,
    supports: {
        features: settings.supports,
    },
};

window.wc.wcBlocksRegistry.registerPaymentMethod( Block_Gateway );

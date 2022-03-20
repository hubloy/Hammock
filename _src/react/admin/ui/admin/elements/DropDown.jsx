import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

export class DropDownUI extends PureComponent{

    constructor(props) {
        super(props);
        this.state = { 
			value: ''
		};
        this.handleChangeEvent = this.handleChangeEvent.bind(this);
	}

	handleChangeEvent(e) {
		this.setState({ value: e.target.value });
		if(typeof this.props.action !== 'undefined' && typeof this.props.action === 'function') {
			var action = this.props.action;
			action( e.target, e.target.value );
		}
    }
    
    render() {
        const value = ( typeof this.state.value === 'undefined' || ! this.state.value ) ? this.props.value : this.state.value,
            blank = typeof this.props.blank !== 'undefined',
            hammock = window.hammock,
            class_name = ( typeof this.props.class_name === 'undefined' || ! this.props.class_name ) ? this.props.name + '-select' : this.props.class_name,
            self = this;
        var data = Array(),
            dropdown = [],
            attributes = this.props.attributes,
            values = this.props.values,
            required = this.props.required === 'undefined' ? false : this.props.required;
        for ( var k in attributes ){
            if ( attributes.hasOwnProperty(k) ) {
                data.push(k+"="+attributes[k]);
            }
        }
        for ( var k in values ){
            if ( values.hasOwnProperty(k) ) {
                dropdown.push(<option value={k} key={k} dangerouslySetInnerHTML={{ __html: values[k] }}/>);
            }
        }
        data = data.join(" ");
        return(
            <select name={self.props.name} value={value} className={"uk-select " + class_name } id="form-horizontal-select" {...data} onChange={self.handleChangeEvent} onSelect={self.handleChangeEvent} required={required}>
                {blank &&
                    <option value=''>{hammock.common.general.select}</option>
                }
                {dropdown}
            </select>
        );
    }
}
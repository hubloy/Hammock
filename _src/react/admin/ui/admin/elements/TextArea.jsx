import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

export class TextAreaUI extends PureComponent {

    constructor(props) {
		super(props);
		this.state = { value: undefined };
		this.onChange = this.onChange.bind(this);
    }

    onChange(e) {
		this.setState({ value: e.target.value });
    }
    
    render() {
        const { name, placeholder = '', class_name = '', required = false } = this.props;
		const value = typeof this.state.value === 'undefined' ? this.props.value : this.state.value;
		const id = typeof this.props.id !== 'undefined' ? this.props.id : name;
        return (
            <textarea name={name} className={'uk-textarea ' +name + ' ' + class_name} id={id} placeholder={placeholder} required={required} onChange={this.onChange} value={value} />
        )
    }
}
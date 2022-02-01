import React, { Component } from 'react';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';


export default class Table extends Component {

    constructor(props) {
		super(props);
		this.state = {
			pager: { current : 1, total : 0, pages : [] },
			items: [],
			columns : [],
			loading : true,
			error : false,
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});

		this.getData = this.getData.bind(this);
	}

	getData = async ( page ) => {
		var type = this.props.type;
		this.fetchWP.get( 'rules/get/' + type + '?page=' + page )
			.then( (json) => {
				this.setState({
					items : json.items,
					pager : json.pager,
					columns : json.columns,
					loading : false,
					error : false,
				});
				window.hammock.helper.select2();
			}, (err) => this.setState({ loading : false, error : true })
		);
	}

	async componentDidMount() {
		this.getData( this.props.page );
	}

	async componentDidUpdate( prevProps ) {
		if ( this.props.type !== prevProps.type ) {
			this.setState({ loading : true, error : false });
			this.getData( this.props.page );
		}
	}

    render() {
		const { pager, items, loading, columns, error } = this.state;
        var hammock = this.props.hammock;
		return (
			<React.Fragment>
				{loading ? (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
						<span className="uk-text-center" uk-spinner="ratio: 3"></span>
					</div>
				) : (
					error ? (
						<h3 className="uk-text-center uk-margin-top">{hammock.error}</h3>
					) : (
						pager.total <= 0 ? (
							<h3 className="uk-text-center uk-margin-top">{hammock.no_data}</h3>
						) : (
							<table className="uk-table uk-background-default">
								<thead>
									<tr>
										<th className='uk-table-shrink'><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
										{Object.keys(columns).map((column) =>
											<th key={column} className={( 'id' == column ) ? "uk-table-shrink hammock-col-" + column : "uk-width-auto hammock-col-" + column}>{columns[column]}</th>
										)}
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th className='uk-table-shrink'><input className="uk-checkbox hammock-bottom-checkbox" type="checkbox" /></th>
										{Object.keys(columns).map((column) =>
											<th key={column} className={( 'id' == column ) ? "uk-table-shrink hammock-col-" + column : "uk-width-auto hammock-col-" + column}>{columns[column]}</th>
										)}
									</tr>
								</tfoot>
								<tbody>
									{Object.keys(items).map((item) =>
										<tr key={items[item].id}>
											<td><input className="uk-checkbox" type="checkbox" value={items[item].id} /></td>
											{Object.keys(columns).map((column) =>
												<td key={column} className={column}><span dangerouslySetInnerHTML={{ __html: items[item][column] }}></span></td>
											)}
										</tr>
									)}
								</tbody>
							</table>
						)
					)
				)}
				<PaginationUI pager={pager} onChange={this.getData} base={this.props.type}/>
			</React.Fragment>
		)
    }
}
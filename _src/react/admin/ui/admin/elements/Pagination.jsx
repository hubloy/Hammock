import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export class PaginationUI extends PureComponent {

	constructor(props) {
		super(props);
		this.onClick = this.onClick.bind(this);
	}

	onClick( e ) {
		if( typeof this.props.onChange !== 'undefined' && typeof this.props.onChange === 'function' ) {
			var action = this.props.onChange;
			action( e.target.dataset.page );
		}
	}
	
	render() {
		const { pager, base } = this.props;
		return(
			<React.Fragment>
				{pager.pages && pager.pages.length > 0 &&
					<ul className="uk-pagination uk-flex-center" uk-margin="">
						<li className={`${pager.current === 1 ? 'uk-disabled' : ''}`}>
							<Link to={{  pathname: `/${base}/page/1` }} data-page={1} onClick={this.onClick}><span uk-icon="chevron-double-left"></span></Link>
						</li>
						<li className={`${pager.current === 1 ? 'uk-disabled' : ''}`}>
							<Link to={{ pathname: `/${base}/page/${Math.abs( pager.current - 1 )}` }} data-page={Math.abs( pager.current - 1 )} onClick={this.onClick}><span uk-icon="chevron-left"></span></Link>
						</li>
						{pager.pages.map(page =>
							<li key={page} className={`${pager.current === page ? 'uk-active' : ''}`}>
								{parseInt(Number(page)) > 0 ? (
									<Link to={{pathname : `/${base}/page/${page}` }} data-page={page} onClick={this.onClick}>{page}</Link>
								): (
									<span>{page}</span>
								)}
							</li>
						)}
						<li className={`${pager.current === pager.total ? 'uk-disabled' : ''}`}>
							<Link to={{ pathname: `/${base}/page/${pager.current + 1}` }} data-page={pager.current + 1} onClick={this.onClick}><span uk-icon="chevron-right"></span></Link>
						</li>
						<li className={`${pager.current === pager.total ? 'uk-disabled' : ''}`}>
							<Link to={{ pathname: `/${base}/page/${pager.total}` }} data-page={pager.total} onClick={this.onClick}><span uk-icon="chevron-double-right"></span></Link>
						</li>
					</ul>
				}
			</React.Fragment>
		)
	}
}
function createSeveralCards(setCardNumber) {
    let inc_card = 16;
    let inx = 0;
    let card_value_list = [];
    //几套
    for (let cardNumber = 1; cardNumber <= setCardNumber; cardNumber++) {
        //花色
        for (let cardColorType = 0; cardColorType < 4; cardColorType++) {
            let index_card = inc_card * inx;
            //牌值
            for (let cards = 1; cards <= 13; cards++) {
                card_value_list[cards + index_card] = cards;
                if (cardColorType === 3 && cards === 13) {
                    let index_XW = inc_card * (inx + 1) - 1;
                    card_value_list[index_XW] = 15;
                    let index_DW = inc_card * (inx + 1);
                    card_value_list[index_DW] = 0;
                }
            }
            inx++;
        }
    }
    return card_value_list;
}
import {Image, Text} from "react-native";
import React from "react";
import { COLORS } from "../../../constants";
import { Layout } from "../../shared";
import { Button } from "react-native-paper";

const img = '../../../assets/reload.png'

export const Team = () => {
  return (
    <Layout>
      <Text style={styles.text}>Бригада еще не сформирована</Text>
      <Image source={require(img)} style={styles.img}/>
      <Button mode="contained-tonal">2121212</Button>
      <Button mode="outlined" raised >
        Press me
      </Button>
    </Layout>
  );
}

const styles = {
  text: {
    fontSize: 30,
    color: COLORS.black,
  },
  img: {
    width: 48,
    height: 48,
    marginLeft: 'auto',
    marginRight: 'auto',
    marginTop: 24
  }
}
